# Kredo — Part 2: API + Site Settings + "Sellable Script" Packaging

This extends `laravel-loan-platform-spec.md`. Same three roles, same tables — this part covers three things:

1. Every endpoint returns JSON via Sanctum (no Blade views/redirects).
2. A `Setting` model the owner uses to brand their instance (site name, logo, contact info).
3. What "package this as a script buyers install themselves" actually requires.

Single-tenant, as agreed: one buyer = one full copy of this codebase on their own server, their own database, their own `settings` row. No shared platform, no tenant_id columns.

---

## 1. Switch to Sanctum

```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

`User` model gets the trait:

```php
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    // ...rest unchanged from Part 1
}
```

`config/sanctum.php` — since there's no SPA frontend baked into this script (buyers may put a mobile app, a separate React app, or nothing in front of it), skip the `stateful` cookie config entirely and issue plain bearer tokens instead. Simpler, and it works the same whether the buyer's frontend is a mobile app or a website on a different domain.

---

## 2. `settings` table — the whole point of this part

### Migration

```php
Schema::create('settings', function (Blueprint $table) {
    $table->id();
    $table->string('site_name')->default('My Loan Business');
    $table->string('tagline')->nullable();
    $table->string('logo_path')->nullable();
    $table->string('favicon_path')->nullable();
    $table->string('primary_color', 7)->default('#C6F135');

    $table->string('contact_phone')->nullable();
    $table->string('contact_email')->nullable();
    $table->string('contact_address')->nullable();
    $table->string('support_whatsapp')->nullable();

    $table->string('facebook_url')->nullable();
    $table->string('twitter_url')->nullable();
    $table->string('instagram_url')->nullable();

    $table->string('currency_symbol', 5)->default('₦');
    $table->string('currency_code', 3)->default('NGN');
    $table->string('license_number')->nullable();   // e.g. their CBN money-lending license no.
    $table->text('footer_text')->nullable();
    $table->text('terms_url')->nullable();
    $table->text('privacy_url')->nullable();

    $table->timestamps();
});
```

One row, ever. Enforce that at the seeder/model level rather than the schema — a unique constraint on nothing is awkward, and "only one row exists" is simpler to guarantee in code.

### `app/Models/Setting.php`

```php
class Setting extends Model
{
    protected $fillable = [
        'site_name', 'tagline', 'logo_path', 'favicon_path', 'primary_color',
        'contact_phone', 'contact_email', 'contact_address', 'support_whatsapp',
        'facebook_url', 'twitter_url', 'instagram_url',
        'currency_symbol', 'currency_code', 'license_number',
        'footer_text', 'terms_url', 'privacy_url',
    ];

    public static function current(): self
    {
        return Cache::rememberForever('site.settings', function () {
            return self::first() ?? self::create([]);
        });
    }

    public function logoUrl(): ?string
    {
        return $this->logo_path ? Storage::disk('public')->url($this->logo_path) : null;
    }

    protected static function booted(): void
    {
        static::updated(fn () => Cache::forget('site.settings'));
    }
}
```

### Helper — `app/helpers.php` (autoload it via `composer.json` → `"files"`)

```php
if (! function_exists('setting')) {
    function setting(string $key, $default = null)
    {
        return data_get(Setting::current(), $key, $default);
    }
}
```

Anywhere in the app — notification templates, PDF receipts, the login page's title tag — you now call `setting('site_name')` instead of hardcoding "Kredo." This is the single change that turns a branded product into a white-label script.

### `app/Http/Controllers/Api/Admin/SettingController.php`

```php
class SettingController extends Controller
{
    public function show()
    {
        return new SettingResource(Setting::current());
    }

    public function update(UpdateSettingRequest $request)
    {
        $setting = Setting::current();
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('branding', 'public');
        }
        if ($request->hasFile('favicon')) {
            $data['favicon_path'] = $request->file('favicon')->store('branding', 'public');
        }

        $setting->update($data);

        return new SettingResource($setting->fresh());
    }
}
```

### `app/Http/Requests/UpdateSettingRequest.php`

```php
class UpdateSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isOwner();
    }

    public function rules(): array
    {
        return [
            'site_name' => 'sometimes|string|max:100',
            'tagline' => 'nullable|string|max:150',
            'logo' => 'nullable|image|max:2048',
            'favicon' => 'nullable|image|max:512',
            'primary_color' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email',
            'contact_address' => 'nullable|string|max:255',
            'support_whatsapp' => 'nullable|string|max:20',
            'facebook_url' => 'nullable|url',
            'twitter_url' => 'nullable|url',
            'instagram_url' => 'nullable|url',
            'currency_symbol' => 'sometimes|string|max:5',
            'currency_code' => 'sometimes|string|size:3',
            'license_number' => 'nullable|string|max:100',
            'footer_text' => 'nullable|string',
            'terms_url' => 'nullable|url',
            'privacy_url' => 'nullable|url',
        ];
    }
}
```

### `app/Http/Resources/SettingResource.php`

```php
class SettingResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'site_name' => $this->site_name,
            'tagline' => $this->tagline,
            'logo_url' => $this->logoUrl(),
            'primary_color' => $this->primary_color,
            'contact' => [
                'phone' => $this->contact_phone,
                'email' => $this->contact_email,
                'address' => $this->contact_address,
                'whatsapp' => $this->support_whatsapp,
            ],
            'socials' => [
                'facebook' => $this->facebook_url,
                'twitter' => $this->twitter_url,
                'instagram' => $this->instagram_url,
            ],
            'currency' => [
                'symbol' => $this->currency_symbol,
                'code' => $this->currency_code,
            ],
            'license_number' => $this->license_number,
            'footer_text' => $this->footer_text,
            'terms_url' => $this->terms_url,
            'privacy_url' => $this->privacy_url,
        ];
    }
}
```

### Route — public read, owner-only write

```php
// Anyone hitting the site needs the branding before they even log in
Route::get('/settings', [Api\SettingController::class, 'show']);

// Only the owner can change it
Route::middleware(['auth:sanctum', 'role:owner'])->group(function () {
    Route::put('/admin/settings', [Api\Admin\SettingController::class, 'update']);
});
```

`GET /settings` being public and unauthenticated matters — your login screen, your public loan-calculator page, your app's splash screen all need the site name/logo *before* anyone has a token.

---

## 3. Converting the rest to API shape

Same controllers as Part 1, three mechanical changes throughout:

1. Return `response()->json(...)` or a `JsonResource` instead of `view(...)`.
2. Auth middleware becomes `auth:sanctum` instead of `auth` + session-cookie checks.
3. Login/register return a token instead of calling `Auth::login()` + session regenerate.

### `app/Http/Controllers/Api/AuthController.php`

```php
class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages(['email' => 'Invalid credentials.']);
        }

        if (in_array($user->role, ['officer', 'owner'])) {
            // single-session: kill every existing token for this user first
            $user->tokens()->delete();
        }

        $token = $user->createToken(
            $request->device_name ?? 'default',
            ['role:' . $user->role]
        )->plainTextToken;

        LoginSession::create([
            'user_id' => $user->id,
            'token' => hash('sha256', explode('|', $token, 2)[1] ?? $token),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'logged_in_at' => now(),
        ]);

        return response()->json([
            'token' => $token,
            'user' => new UserResource($user),
        ]);
    }

    public function register(RegisterBorrowerRequest $request)
    {
        $borrower = User::create([
            ...$request->validated(),
            'role' => 'borrower',
            'password' => Hash::make($request->password),
        ]);

        $token = $borrower->createToken($request->device_name ?? 'default')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => new UserResource($borrower),
        ], 201);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out.']);
    }
}
```

**Single-session, the Sanctum way:** Part 1's `current_session_token` column was built for session-cookie auth. With Sanctum, tokens are already individually identifiable rows in the `personal_access_tokens` table — so `$user->tokens()->delete()` on every officer/owner login is the whole mechanism. Simpler than Part 1's version, and the `login_sessions` table becomes a pure audit log rather than something middleware has to check on every request.

Drop the `EnsureSingleSession` middleware from Part 1 entirely — it's replaced by "delete old tokens at login time." Nothing to check per-request, one token can just work or not.

### Example converted controller — `Api\Officer\LoanController`

```php
class LoanController extends Controller
{
    public function index(Request $request)
    {
        $loans = Loan::visibleTo($request->user())
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->with('borrower')->latest()->paginate(20);

        return LoanResource::collection($loans);
    }

    public function approve(Request $request, Loan $loan)
    {
        $this->authorize('approve', $loan);

        $loan->update([
            'status' => 'approved',
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
        ]);

        return new LoanResource($loan);
    }
}
```

Every other controller from Part 1 follows this exact pattern: same business logic, `view()` → `JsonResource`, `back()->with('status', ...)` → `response()->json([...])`. I haven't re-pasted all of them here since nothing about the logic changes — only the transport.

### `routes/api.php` (replaces the `web.php` group from Part 1)

```php
Route::post('/login', [Api\AuthController::class, 'login']);
Route::post('/register', [Api\AuthController::class, 'register']);
Route::get('/settings', [Api\SettingController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [Api\AuthController::class, 'logout']);
    Route::get('/me', fn (Request $r) => new UserResource($r->user()));

    // ---- Borrower ----
    Route::middleware('role:borrower')->prefix('app')->group(function () {
        Route::get('/loans', [Api\App\LoanController::class, 'index']);
        Route::post('/loans', [Api\App\LoanApplicationController::class, 'store']);
        Route::get('/loans/{loan}', [Api\App\LoanApplicationController::class, 'show']);
        Route::post('/loans/{loan}/repay', [Api\App\RepaymentController::class, 'initiate']);
        Route::post('/kyc', [Api\App\KycController::class, 'store']);
    });

    // ---- Officer + owner ----
    Route::middleware('role:officer,owner')->prefix('officer')->group(function () {
        Route::apiResource('borrowers', Api\Officer\BorrowerController::class)->except('destroy');
        Route::get('/loans', [Api\Officer\LoanController::class, 'index']);
        Route::post('/loans/{loan}/approve', [Api\Officer\LoanController::class, 'approve']);
        Route::post('/loans/{loan}/disburse', [Api\Officer\LoanController::class, 'disburse']);
        Route::post('/loans/{loan}/collections', [Api\Officer\CollectionController::class, 'store']);
    });

    // ---- Owner only ----
    Route::middleware('role:owner')->prefix('admin')->group(function () {
        Route::get('/dashboard', [Api\Admin\DashboardController::class, 'index']);
        Route::apiResource('officers', Api\Admin\OfficerController::class);
        Route::post('/officers/{officer}/deactivate', [Api\Admin\OfficerController::class, 'deactivate']);
        Route::post('/officers/{officer}/reassign', [Api\Admin\OfficerController::class, 'reassignBook']);
        Route::get('/borrowers', [Api\Admin\BorrowerController::class, 'index']);
        Route::get('/loans', [Api\Admin\LoanController::class, 'index']);
        Route::get('/collections', [Api\Admin\CollectionController::class, 'index']);
        Route::get('/reports/officer-performance', [Api\Admin\ReportController::class, 'officerPerformance']);
        Route::put('/settings', [Api\Admin\SettingController::class, 'update']);
    });
});
```

`role` middleware from Part 1 needs one tweak for API responses — return JSON 403 instead of an HTML abort page:

```php
class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        if (! $request->user() || ! in_array($request->user()->role, $roles)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }
        return $next($request);
    }
}
```

---

## 4. Packaging this as a script buyers install themselves

This is the part that turns "a Laravel app" into "a product." Four things:

### a) An install command instead of a manual setup doc

```php
// app/Console/Commands/InstallKredo.php
class InstallKredo extends Command
{
    protected $signature = 'kredo:install';
    protected $description = 'First-time setup: migrate, seed loan products, create the owner account, set site name.';

    public function handle(): void
    {
        $this->info('Setting up your loan platform...');

        Artisan::call('migrate', ['--force' => true]);

        $siteName = $this->ask('What is your business name?');
        Setting::create(['site_name' => $siteName]);

        $ownerName = $this->ask('Your name');
        $ownerEmail = $this->ask('Your email (this is your login)');
        $ownerPassword = $this->secret('Choose a password');

        User::create([
            'name' => $ownerName,
            'email' => $ownerEmail,
            'password' => Hash::make($ownerPassword),
            'role' => 'owner',
        ]);

        $this->call('db:seed', ['--class' => 'LoanProductSeeder']);

        $this->info("Done. Log in at /login as {$ownerEmail}.");
    }
}
```

Buyer's whole setup becomes: upload files, point `.env` at their database, run `php artisan kredo:install`, answer four questions. That's the difference between "a script people can actually use" and "a GitHub repo with a README nobody reads."

### b) `.env.example` — every environment-specific value, nothing hardcoded

```
APP_NAME="Loan Platform"
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

PAYSTACK_PUBLIC_KEY=
PAYSTACK_SECRET_KEY=

MAIL_MAILER=smtp
MAIL_HOST=
MAIL_USERNAME=
MAIL_PASSWORD=

# Optional SMS for repayment reminders
TERMII_API_KEY=
```

Nowhere in the codebase should "Kredo" or your business's Paystack key ever appear literally — everything business-specific lives in `.env` or the `settings` table.

### c) A README the buyer actually needs, not one you need

Requirements, install steps, `kredo:install`, how to set Paystack keys, how to add the first loan product, how to change the logo — written for someone who has a server but has never seen this codebase. This is worth writing as its own document once the app is built; happy to draft it when you're closer to shipping.

### d) What I'd deliberately leave out for now

- **License-key/purchase-code verification** (phone-home to check the buyer paid) — doable later, but it adds real complexity (a licensing server, offline-grace handling, obfuscation debates) for a v1. Ship without it, add it once you have real buyers and a reason to protect against reselling.
- **Automated updates/patching** — for v1, "download the new zip, run new migrations" is fine. Auto-updaters are their own project.
- **Multi-currency** — you already have `currency_symbol`/`currency_code` on `settings`, which covers "buyer in Kenya wants KES instead of NGN." True multi-currency loan math (FX-aware interest) is a different, much bigger feature — not needed for v1.

---

## 5. Updated build order (replaces Part 1 §9)

1. Auth via Sanctum — login/register/logout, token-based single-session for officer/owner.
2. `Setting` model + `kredo:install` command — a buyer can set their business name/logo before touching anything else.
3. Loan products + borrower self-registration + KYC.
4. Officer portal endpoints — borrowers, loans, collections, all `visibleTo()`-scoped.
5. Owner endpoints — dashboard, officer management, reports.
6. Paystack integration (wallet funding + repayment).
7. Notifications (disbursement, repayment reminders) — pull `setting('site_name')` into every template.
8. Write the buyer-facing README + `.env.example`.
9. Package: strip your own `.env`, your own Paystack test keys, your own seeded data — ship a clean zip.
