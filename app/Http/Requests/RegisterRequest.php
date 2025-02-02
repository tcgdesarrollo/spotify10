<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            "name" => ["required", "string", "max:255"],
            'email' => ["required", "string", "email", "max:255", "unique:users"],
            "password" => ["required", "string", "min:3"],
        ];
    }

    public function messages(): array
    {
        return [
            "name.required" => "El nombre es requerido",
            "name.string" => "El nombre debe ser de tipo texto",
            "name.max" => "El nombre debe tener 255 caracteres",
            "email.required" => "Ya existe una cuenta registrada con este correo/email",
            "email.string" => "El email debe ser de tipo texto",
            "email.max" => "El email debe tener 255 caracteres",
            "email.unique" => "El email ya existe",
            "email.email" => "El email debe ser un email",
            "password.required"=>"La contraseña es requerida",
            "password.min" => "La contraseña debe tener al menos 3 caracteres"
        ];
    }
}
