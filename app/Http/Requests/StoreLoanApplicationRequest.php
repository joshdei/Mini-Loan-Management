<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLoanApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isBorrower() === true;
    }

    public function rules(): array
    {
        return [
            'loan_product_id' => ['required', 'exists:loan_products,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'purpose' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
