<?php

namespace App\Http\Controllers\Api\Officer;

use App\Http\Controllers\Controller;
use App\Http\Resources\LoanResource;
use App\Models\Loan;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function index(Request $request)
    {
        $loans = Loan::visibleTo($request->user())
            ->when($request->status, fn ($query, $status) => $query->where('status', $status))
            ->with('borrower', 'product')
            ->latest()
            ->paginate(20);

        return LoanResource::collection($loans);
    }

    public function approve(Request $request, Loan $loan)
    {
        $this->assertVisible($request, $loan);

        if ($loan->status !== 'pending') {
            return response()->json(['message' => 'Only pending loans can be approved.'], 422);
        }

        $loan->update([
            'status' => 'approved',
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
        ]);

        return new LoanResource($loan->fresh()->load('borrower', 'product'));
    }

    public function disburse(Request $request, Loan $loan)
    {
        $this->assertVisible($request, $loan);

        if ($loan->status !== 'approved') {
            return response()->json(['message' => 'Only approved loans can be disbursed.'], 422);
        }

        $loan->update([
            'status' => 'active',
            'disbursed_at' => now(),
            'due_at' => now()->addDays($loan->product->tenure_days),
        ]);

        return new LoanResource($loan->fresh()->load('borrower', 'product'));
    }

    private function assertVisible(Request $request, Loan $loan): void
    {
        abort_if($request->user()->isOfficer() && $loan->officer_id !== $request->user()->id, 404);
    }
}
