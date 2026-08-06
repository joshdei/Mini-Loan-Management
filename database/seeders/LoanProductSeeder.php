<?php

namespace Database\Seeders;

use App\Models\LoanProduct;
use Illuminate\Database\Seeder;

class LoanProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Salary Advance',
                'description' => 'Short-term loan for employed borrowers.',
                'minimum_amount' => 10000,
                'maximum_amount' => 200000,
                'interest_rate' => 10,
                'tenure_days' => 30,
            ],
            [
                'name' => 'SME Working Capital',
                'description' => 'Working capital loan for small businesses.',
                'minimum_amount' => 50000,
                'maximum_amount' => 1000000,
                'interest_rate' => 15,
                'tenure_days' => 90,
            ],
            [
                'name' => 'Market Trader Loan',
                'description' => 'Fast repayment cycle for traders.',
                'minimum_amount' => 5000,
                'maximum_amount' => 100000,
                'interest_rate' => 8,
                'tenure_days' => 21,
            ],
        ];

        foreach ($products as $product) {
            LoanProduct::updateOrCreate(
                ['name' => $product['name']],
                $product + ['is_active' => true],
            );
        }
    }
}
