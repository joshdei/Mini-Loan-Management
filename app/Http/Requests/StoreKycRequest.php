<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreKycRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isBorrower() === true;
    }

    public function rules(): array
    {
        return [
            'bvn' => ['nullable', 'string', 'size:11', Rule::unique('users', 'bvn')->ignore($this->user()->id)],
            'nin' => ['nullable', 'string', 'size:11'],
            'address' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date', 'before:-18 years'],
        ];
    }
}
