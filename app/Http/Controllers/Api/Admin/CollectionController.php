<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CollectionResource;
use App\Models\Collection;
use Illuminate\Http\Request;

class CollectionController extends Controller
{
    public function index(Request $request)
    {
        $collections = Collection::query()
            ->when($request->loan_id, fn ($query, $id) => $query->where('loan_id', $id))
            ->when($request->officer_id, fn ($query, $id) => $query->where('officer_id', $id))
            ->with('loan', 'borrower', 'officer')
            ->latest()
            ->paginate(20);

        return CollectionResource::collection($collections);
    }
}
