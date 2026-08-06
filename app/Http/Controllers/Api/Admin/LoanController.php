<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\LoanResource;
use App\Models\Loan;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function index(Request $request)
    {
        $loans = Loan::query()
            ->when($request->status, fn ($query, $status) => $query->where('status', $status))
            ->when($request->borrower_id, fn ($query, $id) => $query->where('borrower_id', $id))
            ->when($request->officer_id, fn ($query, $id) => $query->where('officer_id', $id))
            ->with('borrower', 'officer', 'product')
            ->latest()
            ->paginate(20);

        return LoanResource::collection($loans);
    }
}
