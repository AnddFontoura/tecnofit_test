<?php

declare(strict_types=1);

namespace App\Request;

use Hyperf\Validation\Request\FormRequest;

class AccountWithdrawRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'method' => 'required|in:PIX',
            'pix.type' => 'required|string',
            'pix.key' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'schedule' => 'nullable|date',
        ];
    }
}
