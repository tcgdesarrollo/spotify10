<?php

namespace App\Console\Commands;

use App\Http\Controllers\UserController;
use App\Mail\LoggedUserMail;
use App\Mail\newPaymentMail;
use App\Mail\NewUser;
use App\Mail\ResetPasswordMail;
use App\Models\Client;
use App\Models\QueueEmail;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class QueueEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:queue-email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $emails = QueueEmail::all();
        foreach ($emails as $email) {
            $this->comment("Procesando el email $email->id");
            $user = User::find($email->user_id);
            //si no tiene usuario asociado o ha sido eliminado, el correo se elimina
            if (!isset($user)) {
                activity()->log('Eliminando el correo de recuperación porque el usuario ha sido eliminado o no se encuentra');
                $email->update(['sent_result' => 'Correo eliminado porque el usuario no fue detectado']);
                $email->delete();
                $this->comment("Eliminando el correo de recuperación porque el usuario ha sido eliminado o no se encuentra");
                continue;
            }
                $email_to_send = $user->email;
            switch ($email->type) {
                case 'reset_password':
                    try {
                        (new UserController())->setToken($user);
                        Mail::to($email_to_send)->queue(new ResetPasswordMail($user->fresh()));
                    } catch (\Exception $e) {
                        $email->update(['sent_result' => $e->getMessage()]);
                        break;
                    }
                    $email->update([
                        'sent_result' => true
                    ]);
                    $email->delete();
                    break;
//                case 'new_payment':
//                    $extra = $email->extra['data'];
//                    $intent = $extra['object'];
//                    if (!isset($intent)){
//                        Log::debug("No hay intent",[$extra]);
//                    }
//                    try {
//                        Mail::to(User::find(1)->email)->queue(new newPaymentMail($user, $intent));
//                    } catch (\Exception $e) {
//                        Log::debug($e->getMessage());
//                        $email->update([
//                            'sent_result' => $e->getMessage(),
//                        ]);
//                        break;
//                    }
//                    $email->update([
//                        'sent_result' => true,
//                    ]);
//                    $email->delete();
//                    break;
                case 'logged_user':
                    try {
                        Mail::to($email_to_send)->queue(new LoggedUserMail($user));
                        $email->update([
                            'sent_result' => true,
                        ]);
                        $email->delete();
                    } catch (\Exception $e) {
                        Log::debug($e->getMessage());
                        $email->update([
                            'sent_result' => $e->getMessage()
                        ]);
                        break;
                    }

                    break;
            }
            sleep(1);
        }

    }
}
