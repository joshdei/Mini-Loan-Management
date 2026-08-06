<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use App\Models\Loan;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        return response()->json([
            'users' => [
                'borrowers' => User::where('role', 'borrower')->count(),
                'officers' => User::where('role', 'officer')->count(),
                'active_officers' => User::where('role', 'officer')->where('is_active', true)->count(),
            ],
            'loans' => [
                'pending' => Loan::where('status', 'pending')->count(),
                'approved' => Loan::where('status', 'approved')->count(),
                'active' => Loan::where('status', 'active')->count(),
                'repaid' => Loan::where('status', 'repaid')->count(),
                'total_principal' => Loan::sum('amount'),
                'total_payable' => Loan::sum('total_payable'),
                'total_repaid' => Loan::sum('amount_repaid'),
            ],
            'collections' => [
                'count' => Collection::count(),
                'total' => Collection::sum('amount'),
            ],
        ]);
    }
}
