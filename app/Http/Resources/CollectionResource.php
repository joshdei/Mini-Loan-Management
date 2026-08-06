<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CollectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'loan_id' => $this->loan_id,
            'borrower_id' => $this->borrower_id,
            'officer_id' => $this->officer_id,
            'amount' => $this->amount,
            'channel' => $this->channel,
            'reference' => $this->reference,
            'notes' => $this->notes,
            'collected_at' => $this->collected_at,
            'loan' => new LoanResource($this->whenLoaded('loan')),
            'borrower' => new UserResource($this->whenLoaded('borrower')),
            'officer' => new UserResource($this->whenLoaded('officer')),
            'created_at' => $this->created_at,
        ];
    }
}
