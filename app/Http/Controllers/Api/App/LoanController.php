<?php

namespace App\Http\Controllers\Api\App;

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
            ->with('product')
            ->latest()
            ->paginate(20);

        return LoanResource::collection($loans);
    }
}
