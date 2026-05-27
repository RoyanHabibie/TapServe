<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user')->id;

        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', "unique:users,email,{$userId}"],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'role'     => ['required', 'in:admin,cashier,kitchen'],
        ];
    }
}
