<?php

namespace App\Http\Controllers\Api\Officer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCollectionRequest;
use App\Http\Resources\CollectionResource;
use App\Models\Collection as LoanCollection;
use App\Models\Loan;

class CollectionController extends Controller
{
    public function store(StoreCollectionRequest $request, Loan $loan)
    {
        abort_if($request->user()->isOfficer() && $loan->officer_id !== $request->user()->id, 404);

        if (! in_array($loan->status, ['active', 'disbursed'], true)) {
            return response()->json(['message' => 'Collections can only be recorded for active loans.'], 422);
        }

        $collection = LoanCollection::create([
            ...$request->validated(),
            'loan_id' => $loan->id,
            'borrower_id' => $loan->borrower_id,
            'officer_id' => $request->user()->id,
            'channel' => $request->channel ?? 'cash',
            'collected_at' => $request->collected_at ?? now(),
        ]);

        $loan->increment('amount_repaid', $collection->amount);

        if ((float) $loan->fresh()->amount_repaid >= (float) $loan->total_payable) {
            $loan->update(['status' => 'repaid']);
        }

        return new CollectionResource($collection->load('loan', 'borrower', 'officer'));
    }
}
