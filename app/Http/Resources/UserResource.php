<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'role' => $this->role,
            'assigned_officer_id' => $this->assigned_officer_id,
            'kyc_status' => $this->kyc_status,
            'staff_code' => $this->staff_code,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
        ];
    }
}
