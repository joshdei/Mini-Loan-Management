<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'loan_product_id' => $this->loan_product_id,
            'borrower_id' => $this->borrower_id,
            'officer_id' => $this->officer_id,
            'amount' => $this->amount,
            'interest_amount' => $this->interest_amount,
            'total_payable' => $this->total_payable,
            'amount_repaid' => $this->amount_repaid,
            'balance' => number_format((float) $this->total_payable - (float) $this->amount_repaid, 2, '.', ''),
            'purpose' => $this->purpose,
            'status' => $this->status,
            'approved_at' => $this->approved_at,
            'disbursed_at' => $this->disbursed_at,
            'due_at' => $this->due_at,
            'product' => new LoanProductResource($this->whenLoaded('product')),
            'borrower' => new UserResource($this->whenLoaded('borrower')),
            'officer' => new UserResource($this->whenLoaded('officer')),
            'created_at' => $this->created_at,
        ];
    }
}
