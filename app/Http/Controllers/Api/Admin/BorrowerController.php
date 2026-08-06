<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class BorrowerController extends Controller
{
    public function index(Request $request)
    {
        $borrowers = User::where('role', 'borrower')
            ->when($request->kyc_status, fn ($query, $status) => $query->where('kyc_status', $status))
            ->when($request->assigned_officer_id, fn ($query, $id) => $query->where('assigned_officer_id', $id))
            ->latest()
            ->paginate(20);

        return UserResource::collection($borrowers);
    }
}
