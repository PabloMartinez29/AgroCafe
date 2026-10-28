<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use App\Services\ConsentService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Solo se piden los datos indispensables para crear la cuenta.
     * El teléfono y la dirección son opcionales y se completan en el perfil.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Password::defaults()],
            ...app(ConsentService::class)->validationRules(),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return app(ConsentService::class)->validationMessages();
    }
}
