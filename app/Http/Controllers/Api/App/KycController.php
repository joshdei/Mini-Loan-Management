<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreKycRequest;
use App\Http\Resources\UserResource;

class KycController extends Controller
{
    public function store(StoreKycRequest $request)
    {
        $user = $request->user();
        $user->update($request->validated() + ['kyc_status' => 'pending']);

        return new UserResource($user->fresh());
    }
}
