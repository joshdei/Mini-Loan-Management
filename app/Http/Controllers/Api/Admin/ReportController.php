<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function officerPerformance(Request $request)
    {
        $from = $request->date('from');
        $to = $request->date('to');

        $officers = User::where('role', 'officer')->get()->map(function (User $officer) use ($from, $to) {
            $loans = Loan::where('officer_id', $officer->id);
            $collections = Collection::where('officer_id', $officer->id);

            if ($from) {
                $loans->whereDate('created_at', '>=', $from);
                $collections->whereDate('collected_at', '>=', $from);
            }

            if ($to) {
                $loans->whereDate('created_at', '<=', $to);
                $collections->whereDate('collected_at', '<=', $to);
            }

            return [
                'officer' => [
                    'id' => $officer->id,
                    'name' => $officer->name,
                    'email' => $officer->email,
                    'staff_code' => $officer->staff_code,
                ],
                'loan_count' => (clone $loans)->count(),
                'active_loan_count' => (clone $loans)->where('status', 'active')->count(),
                'repaid_loan_count' => (clone $loans)->where('status', 'repaid')->count(),
                'principal_total' => (clone $loans)->sum('amount'),
                'collections_total' => (clone $collections)->sum('amount'),
            ];
        });

        return response()->json(['data' => $officers]);
    }
}
