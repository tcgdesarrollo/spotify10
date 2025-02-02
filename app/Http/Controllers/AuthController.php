<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Mail\NewUserMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    /**
     * @throws ValidationException
     */
    public function login(LoginRequest $request): \Illuminate\Foundation\Application|Response|Application|ResponseFactory
    {
        $email = $request->email;
        $user = User::where('email', $email)->first();
        if (!isset($user) || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => [__('wrong_password') . " " . __('contact_admin')],
            ]);
        }
        activity('Login')
            ->causedBy($user)
            ->event('login')
            ->withProperties(['username' => $request->email])
            ->log('Inicio de sesión');
        if (!isset($user->email_verified_at))
            $user->email_verified_at = Carbon::now();
        $user->last_login_date = Carbon::now();
        $user->save();
        (new QueueEmailController())->store('logged_user', $user->id);
        return $this->sendResponse([
            'token' => $user->createToken('web-' . $request->userAgent())->plainTextToken,
            'user' => $user,
        ]);
    }


    /**
     * Get the authenticated User.
     *
     * @return Application|ResponseFactory|\Illuminate\Foundation\Application|Response
     */
    public function me(): Application|ResponseFactory|\Illuminate\Foundation\Application|Response
    {
        $me = auth()->user();
        return $this->sendResponse($me);
    }


    /**
     * Log the user out (Invalidate the token).
     *
     * @param Request $request
     * @return \Illuminate\Foundation\Application|Application|Response|ResponseFactory
     */
    public function logout(Request $request): Application|ResponseFactory|\Illuminate\Foundation\Application|Response
    {
        $request->user()->tokens()->delete();

        return $this->sendResponse(__('logged_out'));
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Foundation\Application|Application|Response|ResponseFactory
     */
    public function refresh(): Application|ResponseFactory|\Illuminate\Foundation\Application|Response
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return Application|\Illuminate\Foundation\Application|Response|ResponseFactory
     */
    protected function respondWithToken(string $token): \Illuminate\Foundation\Application|Response|Application|ResponseFactory
    {
        return $this->sendResponse([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    public function current(): ?Authenticatable
    {
        return auth()->user();
    }

    public function register(RegisterRequest $request)
    {
        $pass = $request->password;
        $request->merge([
            'password' => Hash::make($pass),
            'role_id' => 2
        ]);
        $user = User::create($request->all());

        try {
            Mail::to($user->email)->send(new NewUserMail($user,$pass));
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return $this->sendResponse($user);

    }

    public function reset(Request $request): \Illuminate\Foundation\Application|Response|Application|ResponseFactory
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);
        $user = User::firstWhere(['email' => $request->email]);
        if (isset($user)) {
            $time_allowed = now()->subMinutes(10);
            if (isset($user->last_change_password))
                $user_last_change = Carbon::parse($user->last_change_password);
            else
                $user_last_change = now()->subDays();
            if ($user_last_change->gte($time_allowed)) {
                return $this->sendResponse("Debe esperar al menos 10 minutos para volver a cambiar la contraseña", 400);
            }
            (new QueueEmailController())->store('reset_password', [$user->email], $user->id);
            Artisan::call('app:queue-email');
            return $this->sendResponse("Enviado correo de restauración de contraseña");
        } else {
            return $this->sendResponseForbidden();
        }

    }

    public function identifyUser(Request $request)
    {
        $request->validate([
            'token' => 'required|min:6|max:20'
        ]);
        $user = User::firstWhere('token', $request->token);
        if (isset($user)) {
            return $this->sendResponse($user);
        }
        return $this->sendResponse('El token proporcionado no es válido o caducó', 400);

    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'token' => 'required|min:6|max:20',
            'password' => ['required', Password::defaults()],
        ]);
        $user = User::firstWhere('token', $request->token);
        if (isset($user)) {
            $user->update([
                'password' => Hash::make($request->password)
            ]);
            (new QueueEmailController())->store('change_password', [$user->email], $user->id);
            return $this->sendResponse($user);
        }
        return $this->sendResponse('El token proporcionado no es válido o caducó', 400);

    }


}
