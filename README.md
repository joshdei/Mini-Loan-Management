# Kredo — Self-Hosted Loan Management API

A Laravel API backend for running a loan business: borrowers apply and repay online, field officers manage their own portfolio of borrowers, and the owner sees and controls everything. White-label ready — set your own business name, logo, and contact details in minutes, no code changes required.

Built for lenders who want their own branded platform without building one from scratch: microfinance operators, cooperative societies, salary-advance businesses, and independent loan agents.

---

## What's included

- **Three roles, one login system** — owner, officer, borrower, each with their own scoped view of the data.
- **Officer portfolios** — each officer sees only the borrowers assigned to them and the collections they personally recorded. The owner sees everyone's.
- **One active session per staff member** — an officer or owner logging in on a new device automatically signs them out everywhere else.
- **Borrower self-service** — registration, KYC capture, loan application, and repayment, all via the API.
- **Officer + owner loan workflow** — review, approve (with an officer approval ceiling), disburse, and track status through to completion or default.
- **Cash and online collections** — officers can record field/cash collections; borrowers can repay directly through Paystack.
- **Owner dashboard & reports** — total disbursed, total collected, active/overdue counts, per-officer performance.
- **White-label branding** — site name, logo, favicon, brand color, contact info, and social links are all controlled from a single `settings` record, not hardcoded.
- **Guided first-time setup** — one Artisan command walks a new owner through creating their business profile and admin account.

## What's not included (yet)

- No frontend. This is a JSON API only — pair it with a mobile app, a web app, or whatever client you build.
- No license-key/purchase verification.
- No multi-tenancy. Each installation serves one business. If you need one platform serving many companies, that's a different (larger) build.

---

## Tech stack

| | |
|---|---|
| Framework | Laravel 11 / 12 |
| PHP | 8.3+ |
| Database | MySQL / MariaDB |
| Auth | Laravel Sanctum (bearer tokens) |
| Payments | Paystack |
| Notifications | Mail, with optional SMS via Termii |

---

## Requirements

- PHP 8.3 or higher
- Composer
- MySQL 8+ or MariaDB 10.4+
- A Paystack account (test keys are fine for development)

---

## Installation

```bash
git clone https://github.com/your-org/kredo.git
cd kredo
composer install
cp .env.example .env
php artisan key:generate
```

Open `.env` and set your database credentials and Paystack keys:

```
DB_DATABASE=kredo
DB_USERNAME=root
DB_PASSWORD=

PAYSTACK_PUBLIC_KEY=
PAYSTACK_SECRET_KEY=
```

Then run the guided setup — this migrates the database, seeds default loan products, and walks you through creating your business profile and owner account:

```bash
php artisan kredo:install
```

You'll be asked for:

1. Your business name
2. Your name and email (this becomes your owner login)
3. A password

Once it finishes, log in via `POST /login` with the email and password you chose.

---

## Configuration & branding

All business-facing settings — site name, logo, contact phone/email, brand color, social links, currency symbol — live in one `settings` record, editable by the owner:

```
GET  /settings            → public, no auth required (used by login screens, splash screens, etc.)
PUT  /admin/settings       → owner only, multipart form for logo/favicon uploads
```

Nothing in the codebase should reference a hardcoded business name. If you're customizing this for your own brand, everything you need to change lives in that one table — not in the source files.

---

## Roles

| Role | Access |
|---|---|
| `owner` | Full visibility — every officer, every borrower, every loan, every collection. Manages officer accounts, reassigns portfolios, approves loans above an officer's limit, edits site settings. |
| `officer` | Sees only borrowers assigned to them and loans/collections tied to those borrowers. Can record cash collections and approve loans up to a fixed ceiling. One active session at a time. |
| `borrower` | Self-registers, completes KYC, applies for loans, repays via Paystack. Sees only their own data. |

---

## API overview

Base auth flow:

```
POST /register          → borrower self-registration, returns a bearer token
POST /login              → returns a bearer token (kills existing sessions for officer/owner)
POST /logout              → revokes the current token
GET  /me                  → the authenticated user
```

Grouped by role:

```
/app/*        → borrower endpoints (loans, KYC, repayment)      — role: borrower
/officer/*    → officer endpoints (borrowers, loans, collections) — role: officer, owner
/admin/*      → owner-only endpoints (officers, reports, settings) — role: owner
```

All authenticated requests require:

```
Authorization: Bearer {token}
Accept: application/json
```

Full endpoint-by-endpoint documentation with request/response payloads lives in [`docs/API.md`](docs/API.md).

---

## Project structure

```
app/
  Console/Commands/InstallKredo.php
  Http/
    Controllers/Api/
      AuthController.php
      SettingController.php
      App/           # borrower-facing
      Officer/        # officer-facing
      Admin/          # owner-facing
    Middleware/
      EnsureRole.php
    Requests/
    Resources/
  Models/
    User.php
    Loan.php
    LoanProduct.php
    Collection.php
    Setting.php
    LoginSession.php
  Policies/
    LoanPolicy.php
    UserPolicy.php
database/
  migrations/
  seeders/
    LoanProductSeeder.php
routes/
  api.php
```

---

## Security notes

- BVN and NIN fields are stored as-is for KYC purposes — if you're deploying this in production, review your data protection obligations (NDPR in Nigeria) around encryption at rest for these fields before going live.
- Cash collections recorded by officers happen outside Paystack. Reconcile them against actual bank deposits regularly — the schema includes fields to support this, but reconciliation is a process you run, not something the code enforces automatically.
- Rotate Paystack keys and rate-limit the login endpoint before deploying publicly.

---

## License

Specify your license here (MIT, proprietary, commercial-use-only, etc.) depending on how you intend to distribute this.

---

## Support

For issues or questions, open a GitHub issue or contact joshuadeinne@gmail.com.
