# Loan Platform API

Single-tenant Laravel loan-management API for a lender that wants its own install, database, branding, officers, borrowers, loans, collections, and reports.

## Requirements

- PHP 8.3+
- Composer
- MySQL or MariaDB
- Web server pointing to `public/`
- Writable `storage/` and `bootstrap/cache/`

## First Install

1. Upload the project to your server.
2. Copy `.env.example` to `.env`.
3. Fill in database, mail, Paystack, and `APP_URL` values.
4. Run:

```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan storage:link
php artisan kredo:install
```

The install command runs migrations, creates the first owner account, creates the settings row, and seeds default loan products.

## API Basics

All endpoints return JSON. Authenticated endpoints use Sanctum bearer tokens:

```http
Authorization: Bearer YOUR_TOKEN
Accept: application/json
```

Public endpoints:

- `POST /api/register`
- `POST /api/login`
- `GET /api/settings`
- `GET /api/loan-products`

Authenticated common endpoints:

- `POST /api/logout`
- `GET /api/me`

Borrower endpoints:

- `GET /api/app/loans`
- `POST /api/app/loans`
- `GET /api/app/loans/{loan}`
- `POST /api/app/loans/{loan}/repay`
- `POST /api/app/kyc`

Officer/owner endpoints:

- `GET /api/officer/borrowers`
- `POST /api/officer/borrowers`
- `GET /api/officer/loans`
- `POST /api/officer/loans/{loan}/approve`
- `POST /api/officer/loans/{loan}/disburse`
- `POST /api/officer/loans/{loan}/collections`

Owner endpoints:

- `GET /api/admin/dashboard`
- `apiResource /api/admin/officers`
- `POST /api/admin/officers/{officer}/deactivate`
- `POST /api/admin/officers/{officer}/reassign`
- `GET /api/admin/borrowers`
- `GET /api/admin/loans`
- `GET /api/admin/collections`
- `GET /api/admin/reports/officer-performance`
- `PUT /api/admin/settings`

## Branding

Use `PUT /api/admin/settings` as the owner to change site name, logo, favicon, color, contact info, socials, currency, license number, footer text, terms URL, and privacy URL.

Use `GET /api/settings` before login so a mobile app, React app, or public landing page can render buyer-specific branding.

## Packaging Notes

Before shipping a buyer zip, remove your real `.env`, database dumps, logs, Paystack keys, and test data. Ship `.env.example`, source files, migrations, seeders, and this README.
