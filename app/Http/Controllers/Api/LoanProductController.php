<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LoanProductResource;
use App\Models\LoanProduct;

class LoanProductController extends Controller
{
    public function index()
    {
        return LoanProductResource::collection(
            LoanProduct::where('is_active', true)->latest()->get()
        );
    }
}
