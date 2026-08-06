# Build Progress

Current goal: Build the Laravel loan platform as a JSON API using Sanctum, settings branding, loan management endpoints, and buyer-install packaging.

## Completed

- Read `laravel-loan-platform-spec.md`.
- Confirmed this is a fresh Laravel app with Sanctum already installed and published.
- Confirmed the current `routes/api.php` and old auth controller were session/redirect-based and need replacement.
- Added Sanctum `HasApiTokens` support to `app/Models/User.php`.
- Added domain models:
  - `Setting`
  - `LoanProduct`
  - `Loan`
  - `Collection`
  - `LoginSession`
- Added `app/helpers.php` with the global `setting()` helper.
- Registered `app/helpers.php` in Composer autoload files.
- Added migrations for:
  - `settings`
  - `loan_products`
  - `loans`
  - `collections`
  - `login_sessions`
- Added API request validation classes for auth, settings, KYC, loan applications, and collections.
- Added JSON resources for users, settings, loan products, loans, and collections.
- Added role middleware and registered it as `role`.
- Added API controllers for:
  - Auth
  - Public settings
  - Public loan products
  - Borrower loan/KYC/repayment flows
  - Officer borrower/loan/collection flows
  - Owner dashboard, officer management, lists, reports, and settings update
- Replaced `routes/api.php` with Sanctum JSON API routes.
- Added `kredo:install` command.
- Added default `LoanProductSeeder` and wired it into `DatabaseSeeder`.
- Rewrote `.env.example` for a buyer install.
- Rewrote `README.md` with install and API usage instructions.
- Verified `composer dump-autoload` succeeds.
- Verified `php artisan route:list --path=api` lists 32 API routes.
- Verified migrations succeed against an isolated SQLite database.
- Added focused API feature tests covering borrower registration, public settings, owner settings update, and owner single-session token behavior.
- Verified `php artisan test` passes with 6 tests and 19 assertions.
- Ran Laravel Pint and verified `vendor\bin\pint --test` passes.
- Local MySQL migration was attempted but could not connect to `127.0.0.1:3306` for database `loan`; verification used SQLite in memory instead.

## In Progress

- None.

## Next

- Start MySQL/Laragon and run `php artisan migrate --force` against the real configured database.
- Run `php artisan kredo:install` to create the buyer settings row, first owner account, and default loan products.
- Connect real Paystack payment initialization/verification when payment keys and desired flow are ready.
