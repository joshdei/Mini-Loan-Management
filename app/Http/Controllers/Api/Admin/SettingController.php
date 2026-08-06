<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSettingRequest;
use App\Http\Resources\SettingResource;
use App\Models\Setting;

class SettingController extends Controller
{
    public function update(UpdateSettingRequest $request)
    {
        $setting = Setting::current();
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('branding', 'public');
        }

        if ($request->hasFile('favicon')) {
            $data['favicon_path'] = $request->file('favicon')->store('branding', 'public');
        }

        unset($data['logo'], $data['favicon']);

        $setting->update($data);

        return new SettingResource($setting->fresh());
    }
}
