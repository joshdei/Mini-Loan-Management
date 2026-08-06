<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SettingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'site_name' => $this->site_name,
            'tagline' => $this->tagline,
            'logo_url' => $this->logoUrl(),
            'favicon_url' => $this->faviconUrl(),
            'primary_color' => $this->primary_color,
            'contact' => [
                'phone' => $this->contact_phone,
                'email' => $this->contact_email,
                'address' => $this->contact_address,
                'whatsapp' => $this->support_whatsapp,
            ],
            'socials' => [
                'facebook' => $this->facebook_url,
                'twitter' => $this->twitter_url,
                'instagram' => $this->instagram_url,
            ],
            'currency' => [
                'symbol' => $this->currency_symbol,
                'code' => $this->currency_code,
            ],
            'license_number' => $this->license_number,
            'footer_text' => $this->footer_text,
            'terms_url' => $this->terms_url,
            'privacy_url' => $this->privacy_url,
        ];
    }
}
