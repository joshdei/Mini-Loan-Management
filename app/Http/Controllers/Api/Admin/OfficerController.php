<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class OfficerController extends Controller
{
    public function index()
    {
        return UserResource::collection(
            User::where('role', 'officer')->latest()->paginate(20)
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20', 'unique:users,phone'],
            'staff_code' => ['nullable', 'string', 'max:50', 'unique:users,staff_code'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $officer = User::create([
            ...$data,
            'password' => Hash::make($data['password']),
            'role' => 'officer',
            'is_active' => true,
        ]);

        return new UserResource($officer);
    }

    public function show(User $officer)
    {
        abort_unless($officer->isOfficer(), 404);

        return new UserResource($officer);
    }

    public function update(Request $request, User $officer)
    {
        abort_unless($officer->isOfficer(), 404);

        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', Rule::unique('users', 'email')->ignore($officer->id)],
            'phone' => ['nullable', 'string', 'max:20', Rule::unique('users', 'phone')->ignore($officer->id)],
            'staff_code' => ['nullable', 'string', 'max:50', Rule::unique('users', 'staff_code')->ignore($officer->id)],
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $officer->update($data);

        return new UserResource($officer->fresh());
    }

    public function destroy(User $officer)
    {
        abort_unless($officer->isOfficer(), 404);

        $officer->delete();

        return response()->json(['message' => 'Officer deleted.']);
    }

    public function deactivate(User $officer)
    {
        abort_unless($officer->isOfficer(), 404);

        $officer->update(['is_active' => false]);
        $officer->tokens()->delete();

        return new UserResource($officer->fresh());
    }

    public function reassignBook(Request $request, User $officer)
    {
        abort_unless($officer->isOfficer(), 404);

        $data = $request->validate([
            'to_officer_id' => ['required', 'exists:users,id'],
        ]);

        $target = User::findOrFail($data['to_officer_id']);
        abort_unless($target->isOfficer(), 422, 'Target user must be an officer.');

        User::where('role', 'borrower')
            ->where('assigned_officer_id', $officer->id)
            ->update(['assigned_officer_id' => $target->id]);

        return response()->json(['message' => 'Borrowers reassigned.']);
    }
}
