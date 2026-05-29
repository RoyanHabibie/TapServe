<?php

namespace App\Http\Requests;

use App\Models\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;

class ProcessPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $codes = PaymentMethod::where('shop_id', auth()->user()->shop_id)
            ->where('is_active', true)
            ->pluck('code')
            ->toArray();

        return [
            'amount' => ['required', 'numeric', 'min:1'],
            'method' => ['required', 'in:' . implode(',', $codes)],
        ];
    }
}
