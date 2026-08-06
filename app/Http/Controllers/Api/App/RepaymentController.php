<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Http\Resources\LoanResource;
use App\Models\Loan;
use Illuminate\Http\Request;

class RepaymentController extends Controller
{
    public function initiate(Request $request, Loan $loan)
    {
        abort_unless($loan->borrower_id === $request->user()->id, 404);

        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'channel' => ['nullable', 'string', 'max:50'],
        ]);

        return response()->json([
            'message' => 'Repayment initialized. Connect Paystack to complete live payments.',
            'loan' => new LoanResource($loan),
            'payment' => [
                'amount' => $data['amount'],
                'channel' => $data['channel'] ?? 'paystack',
                'reference' => 'REP-'.now()->format('YmdHis').'-'.$loan->id,
            ],
        ]);
    }
}
