<?php

namespace App\Console\Commands;

use App\Models\Setting;
use App\Models\User;
use Database\Seeders\LoanProductSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

class InstallKredo extends Command
{
    protected $signature = 'kredo:install';

    protected $description = 'First-time setup for the loan platform.';

    public function handle(): int
    {
        $this->info('Setting up your loan platform...');

        Artisan::call('migrate', ['--force' => true]);
        $this->line(Artisan::output());

        $siteName = $this->ask('What is your business name?', 'My Loan Business');
        Setting::query()->firstOrCreate([], ['site_name' => $siteName]);

        $ownerEmail = $this->ask('Owner email/login');

        if (! $ownerEmail) {
            $this->error('Owner email is required.');

            return self::FAILURE;
        }

        if (! User::where('email', $ownerEmail)->exists()) {
            $ownerName = $this->ask('Owner name', 'Owner');
            $ownerPassword = $this->secret('Choose owner password');

            if (! $ownerPassword) {
                $this->error('Owner password is required.');

                return self::FAILURE;
            }

            User::create([
                'name' => $ownerName,
                'email' => $ownerEmail,
                'password' => Hash::make($ownerPassword),
                'role' => 'owner',
                'is_active' => true,
                'kyc_status' => 'verified',
            ]);
        }

        $this->call('db:seed', ['--class' => LoanProductSeeder::class, '--force' => true]);

        $this->info('Done. Use POST /api/login with the owner email and password.');

        return self::SUCCESS;
    }
}
