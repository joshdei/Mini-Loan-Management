<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLoanApplicationRequest;
use App\Http\Resources\LoanResource;
use App\Models\Loan;
use App\Models\LoanProduct;

class LoanApplicationController extends Controller
{
    public function store(StoreLoanApplicationRequest $request)
    {
        $product = LoanProduct::where('is_active', true)->findOrFail($request->loan_product_id);
        $amount = (float) $request->amount;

        if ($amount < (float) $product->minimum_amount || $amount > (float) $product->maximum_amount) {
            return response()->json(['message' => 'Amount is outside this loan product range.'], 422);
        }

        $interestAmount = round($amount * ((float) $product->interest_rate / 100), 2);

        $loan = Loan::create([
            'loan_product_id' => $product->id,
            'borrower_id' => $request->user()->id,
            'officer_id' => $request->user()->assigned_officer_id,
            'amount' => $amount,
            'interest_amount' => $interestAmount,
            'total_payable' => $amount + $interestAmount,
            'purpose' => $request->purpose,
            'status' => 'pending',
        ]);

        return new LoanResource($loan->load('product'));
    }

    public function show(Loan $loan)
    {
        abort_unless($loan->borrower_id === request()->user()->id, 404);

        return new LoanResource($loan->load('product', 'collections'));
    }
}
