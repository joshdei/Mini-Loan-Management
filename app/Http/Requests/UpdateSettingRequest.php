<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isOwner() === true;
    }

    public function rules(): array
    {
        return [
            'site_name' => ['sometimes', 'string', 'max:100'],
            'tagline' => ['nullable', 'string', 'max:150'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'favicon' => ['nullable', 'image', 'max:512'],
            'primary_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'contact_phone' => ['nullable', 'string', 'max:20'],
            'contact_email' => ['nullable', 'email'],
            'contact_address' => ['nullable', 'string', 'max:255'],
            'support_whatsapp' => ['nullable', 'string', 'max:20'],
            'facebook_url' => ['nullable', 'url'],
            'twitter_url' => ['nullable', 'url'],
            'instagram_url' => ['nullable', 'url'],
            'currency_symbol' => ['sometimes', 'string', 'max:5'],
            'currency_code' => ['sometimes', 'string', 'size:3'],
            'license_number' => ['nullable', 'string', 'max:100'],
            'footer_text' => ['nullable', 'string'],
            'terms_url' => ['nullable', 'url'],
            'privacy_url' => ['nullable', 'url'],
        ];
    }
}
