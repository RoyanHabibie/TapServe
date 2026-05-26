<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TableUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'capacity' => ['sometimes', 'integer', 'min:1'],
            'status' => ['nullable', 'in:available,occupied,disabled'],
        ];
    }
}
