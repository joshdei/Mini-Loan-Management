<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class Setting extends Model
{
    protected $fillable = [
        'site_name',
        'tagline',
        'logo_path',
        'favicon_path',
        'primary_color',
        'contact_phone',
        'contact_email',
        'contact_address',
        'support_whatsapp',
        'facebook_url',
        'twitter_url',
        'instagram_url',
        'currency_symbol',
        'currency_code',
        'license_number',
        'footer_text',
        'terms_url',
        'privacy_url',
    ];

    public static function current(): self
    {
        return Cache::rememberForever('site.settings', function () {
            $setting = self::first();

            if ($setting) {
                return $setting;
            }

            return self::create([])->fresh();
        });
    }

    public function logoUrl(): ?string
    {
        return $this->logo_path ? Storage::disk('public')->url($this->logo_path) : null;
    }

    public function faviconUrl(): ?string
    {
        return $this->favicon_path ? Storage::disk('public')->url($this->favicon_path) : null;
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('site.settings'));
        static::deleted(fn () => Cache::forget('site.settings'));
    }
}
