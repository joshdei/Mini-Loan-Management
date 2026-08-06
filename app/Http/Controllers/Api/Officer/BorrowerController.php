<?php

namespace App\Http\Controllers\Api\Officer;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class BorrowerController extends Controller
{
    public function index(Request $request)
    {
        $borrowers = User::where('role', 'borrower')
            ->when($request->user()->isOfficer(), fn ($query) => $query->where('assigned_officer_id', $request->user()->id))
            ->latest()
            ->paginate(20);

        return UserResource::collection($borrowers);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20', 'unique:users,phone'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'assigned_officer_id' => ['nullable', 'exists:users,id'],
        ]);

        $borrower = User::create([
            ...$data,
            'password' => Hash::make($data['password']),
            'role' => 'borrower',
            'assigned_officer_id' => $request->user()->isOfficer() ? $request->user()->id : ($data['assigned_officer_id'] ?? null),
        ]);

        return new UserResource($borrower);
    }

    public function show(Request $request, User $borrower)
    {
        $this->assertBorrowerVisible($request, $borrower);

        return new UserResource($borrower);
    }

    public function update(Request $request, User $borrower)
    {
        $this->assertBorrowerVisible($request, $borrower);

        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', Rule::unique('users', 'email')->ignore($borrower->id)],
            'phone' => ['nullable', 'string', 'max:20', Rule::unique('users', 'phone')->ignore($borrower->id)],
            'address' => ['nullable', 'string', 'max:255'],
            'kyc_status' => ['sometimes', Rule::in(['pending', 'verified', 'rejected'])],
        ]);

        $borrower->update($data);

        return new UserResource($borrower->fresh());
    }

    private function assertBorrowerVisible(Request $request, User $borrower): void
    {
        abort_unless($borrower->isBorrower(), 404);
        abort_if($request->user()->isOfficer() && $borrower->assigned_officer_id !== $request->user()->id, 404);
    }
}
