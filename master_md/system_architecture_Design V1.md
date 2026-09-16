# System Architecture & Technical Specifications: Modular Laravel Event & Payment Engine for WordPress

This system architecture design establishes a hybrid implementation model, integrating a portable Laravel 10.x transaction engine into an existing WordPress installation on a single shared MySQL database.

---

## Deliverable 1: Proposal Coverage & Traceability Matrix

| Requirement Category | Proposal Requirement | Technical Architecture Solution |
| :--- | :--- | :--- |
| **Payment Processing** | M-Pesa STK Push (Safaricom Daraja API) & Card (Stripe/Flutterwave) payments. | Modular `PaymentGatewayInterface` with dedicated `MpesaGateway` and `CardGateway` drivers managed via `PaymentFactory`. |
| **Financial Integrity** | Payment state machine (AWAITING_PAYMENT → PROCESSING → SUCCESS/FAILED) & duplicate callback prevention. | UUID v4 transaction tracking (`payment_uuid`) acting as idempotency keys; database state locks on `evt_payments`. |
| **Ticketing & Delivery** | Asynchronous PDF ticket generation with unique alphanumeric codes and automated email dispatch. | DomPDF ticket renderer dispatched via background Redis queues (`GenerateTicketPdf` job) with QR code generation. |
| **Data Coexistence** | Single database deployment; zero data silos; leverage existing `wp_users` table. | Eloquent ORM mapping to existing WordPress tables (`wp_users`, `wp_usermeta`) coexisting with custom `evt_*` indexed tables. |
| **Engine Portability** | Modular application structure for reuse across multiple WordPress sites without code rewrites. | Standalone Composer package (`icrhk/wp-event-engine`) loaded via a light WordPress MU-Plugin bootstrap bridge. |
| **Admin Management** | Native WordPress admin interface for event CRUD, payment monitoring, ticket management, and exports. | WordPress Custom Admin Plugin (`icrhk-events`) consuming internal Laravel API endpoints with role-based access control. |
| **Audit & Compliance** | Immutable forensic audit trails for all payment transactions and gateway raw payloads. | Dedicated `evt_payment_logs` table capturing raw JSON payloads, retries, errors, and timestamps. |

---

## Deliverable 2: Environment & Local Setup Requirements

### Core System Dependencies
* **PHP:** v8.2 or higher
* **Database:** MySQL 8.0+ or MariaDB 10.5+ (Existing WordPress database)
* **In-Memory Store:** Redis 7.x (for Queue management & caching)
* **Package Managers:** Composer 2.x, Node.js (v18+) & NPM
* **PHP Extensions:** `pdo_mysql`, `redis`, `gd` or `imagick` (for QR code generation), `bcmath` (for high-precision financial calculations), `mbstring`, `curl`, `xml`, `zip`.

### Local Setup Instructions

#### Step 1: Directory Structure Alignment
Place the Laravel transaction engine inside the existing local WordPress environment structure without exposing private framework directory roots publicly:

```text
/public_html (WordPress Root)
├── wp-config.php
├── wp-content/
│   ├── plugins/
│   │   └── icrhk-events/             # Admin UI & Shortcodes
│   └── mu-plugins/
│       └── icrhk-laravel-bridge.php  # Bootstrapper
└── laravel-engine/                   # Portable Transaction Engine
    ├── app/
    ├── config/
    ├── database/
    ├── routes/
    └── .env
```

#### Step 2: Configure Environment (`laravel-engine/.env`)
Configure the Laravel instance to connect directly to the active local WordPress database:

```ini
APP_NAME="ICRHK Event Engine"
APP_ENV=local
APP_KEY=base64:GENERATE_WITH_ARTISAN_KEY_GENERATE
APP_DEBUG=true
APP_URL=http://localhost

# Shared WordPress Database Connection
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=icrhk_wordpress_db
DB_USERNAME=root
DB_PASSWORD=root
DB_PREFIX=               # Set empty; prefixes defined per Eloquent Model

# Queue & Cache Configuration
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Gateway Credentials
MPESA_ENV=sandbox
MPESA_CONSUMER_KEY=your_sandbox_key
MPESA_CONSUMER_SECRET=your_sandbox_secret
MPESA_SHORTCODE=174379
MPESA_PASSKEY=bfb29e629281777...
```

#### Step 3: Web Server Setup (Nginx VHost Configuration)
Configure Nginx to route standard web traffic to WordPress while passing `/api/v1/events` routing directly to the Laravel entry point (`laravel-engine/public/index.php`).

```nginx
server {
    listen 80;
    server_name icrhk.test;
    root /var/www/icrhk.org;
    index index.php index.html;

    # Direct API requests to Laravel Public Entrypoint
    location /api/v1/events {
        alias /var/www/icrhk.org/laravel-engine/public;
        try_files $uri $uri/ @laravel;

        location ~ \.php$ {
            include fastcgi_params;
            fastcgi_param SCRIPT_FILENAME /var/www/icrhk.org/laravel-engine/public/index.php;
            fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        }
    }

    location @laravel {
        rewrite /api/v1/events/(.*)$ /index.php?/$1 last;
    }

    # Standard WordPress Routing
    location / {
        try_files $uri $uri/ /index.php?$args;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
    }
}
```

---

## Deliverable 3: System Architecture & Database Design

```
+-----------------------------------------------------------------------+
|                        ICRHK.ORG HTTP Request                         |
+-----------------------------------------------------------------------+
                                   |
         +-------------------------+-------------------------+
         |                                                   |
         v (Web / Admin UI)                                  v (API / Webhooks)
+----------------------------------+       +----------------------------------+
|      WordPress Core Architecture |       |      Laravel 10.x Engine         |
|  - Custom Admin Plugin UI        |       |  - API Routing & Controllers     |
|  - Theme Templates & Shortcodes  |       |  - Payment State Machine         |
|  - User Cookie Auth Management   |       |  - Daraja & Card SDKs            |
+----------------------------------+       +----------------------------------+
                 |                                           |
                 +--------------------+----------------------+
                                      |
                                      v
+-----------------------------------------------------------------------+
|                     Shared Database & Queue Infrastructure            |
|  - Shared MySQL Instance (wp_users, wp_options, custom evt_*)         |
|  - Redis Instance (Jobs: GenerateTicketPdf, SendConfirmationEmail)    |
+-----------------------------------------------------------------------+
```

### Data Layer Strategy (Eloquent to WordPress Schema Integration)
To read and write seamlessly to the WordPress database without altering WordPress tables or breaking hook lifecycles, models are mapped directly using designated primary keys and table specifications.

#### Eloquent Model Mapping Example: `WpUser.php`
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WpUser extends Model
{
    protected $table = 'wp_users';
    protected $primaryKey = 'ID';
    public $timestamps = false; // WordPress manages user timestamps via user_registered

    protected $fillable = ['user_login', 'user_email', 'user_pass', 'display_name'];

    public function registrations()
    {
        return $this->hasMany(Registration::class, 'user_id', 'ID');
    }
}
```

#### Schema Migration Strategy: `evt_*` Tables
Custom event tables use the `evt_` prefix to prevent namespace collisions during WordPress core or third-party plugin updates.

```php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('evt_events', function (Blueprint $table) {
            $table->id();
            $table->uuid('event_uuid')->unique(); // Unique public lookup identifier
            $table->string('name');
            $table->string('slug')->unique();
            $table->dateTime('date');
            $table->string('venue');
            $table->string('status')->default('draft');
            $table->integer('max_attendees')->unsigned()->default(0);
            $table->timestamps();
            
            $table->index(['status', 'date']);
        });

        Schema::create('evt_registrations', function (Blueprint $table) {
            $table->id();
            $table->uuid('registration_uuid')->unique();
            $table->foreignId('event_id')->constrained('evt_events')->onDelete('cascade');
            $table->unsignedBigInteger('user_id')->nullable(); // Foreign Key to wp_users.ID
            $table->string('full_name');
            $table->string('email');
            $table->string('phone', 20);
            $table->string('status')->default('awaiting_payment');
            $table->timestamps();

            $table->foreign('user_id')->references('ID')->on('wp_users')->onDelete('set null');
            $table->index(['event_id', 'status']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('evt_registrations');
        Schema::dropIfExists('evt_events');
    }
};
```

### Package Structure (Portable Engine Architecture)
The Laravel engine can be extracted into an independent Git repository (`icrhk/wp-event-engine`) and pulled into any WordPress environment via Composer:

```text
wp-event-engine/
├── composer.json
├── config/
│   └── event-engine.php
├── database/
│   └── migrations/
├── src/
│   ├── EventEngineServiceProvider.php
│   ├── Http/
│   │   ├── Controllers/
│   │   └── Middleware/
│   ├── Models/
│   ├── Services/
│   │   ├── Payment/
│   │   └── Ticket/
│   └── Support/
│       └── WpBridge.php
└── routes/
    └── api.php
```

#### Bootstrap Bridge: `wp-content/mu-plugins/icrhk-laravel-bridge.php`
```php
<?php
/**
 * Plugin Name: ICRHK Laravel Engine Bridge
 * Description: Initializes the Laravel framework kernel inside WordPress context.
 */

if (!defined('ABSPATH')) exit;

require_once ABSPATH . '../laravel-engine/vendor/autoload.php';

$app = require_once ABSPATH . '../laravel-engine/bootstrap/app.php';

// Bootstrap Laravel Kernel globally without taking over standard HTTP routing
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->bootstrap();
```

---

## Deliverable 4: Reliability & Maintenance Plan

### WordPress Version Resilience Strategy
1. **Isolation of Custom Schemas:** All domain-specific data operations reside within `evt_*` custom tables managed strictly by Laravel Eloquent Migrations. WordPress Core updates will never alter or drop these tables.
2. **Read-Only Operations on Core WordPress Tables:** The Laravel layer interacts with `wp_users` and `wp_usermeta` purely as a read-heavy interface. User creation or credential modification requests are delegated back to native WordPress functions (`wp_insert_user()`, `wp_set_password()`) via the `WpBridge` class to guarantee forward compatibility with future WP updates.

### Unified Session & Authentication Sharing
Shared authentication leverages WordPress user login cookies within Laravel middleware:

```php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\WpUser;
use Illuminate\Support\Facades\Auth;

class AuthenticateWithWordPress
{
    public function handle(Request $request, Closure $next)
    {
        // Check if WordPress function exists (bootstrapped environment)
        if (function_exists('wp_validate_auth_cookie')) {
            $wpUserId = wp_validate_auth_cookie('', 'logged_in');

            if ($wpUserId) {
                $user = WpUser::find($wpUserId);
                if ($user) {
                    // Log user into Laravel Auth guard dynamically
                    Auth::setUser($user);
                }
            }
        }

        return $next($request);
    }
}
```

### Transaction State Machine & Idempotency Safeguards

```
[User Form Submission] 
          |
          v
(Status: AWAITING_PAYMENT) ---> [Generate payment_uuid (UUID v4)]
          |                                     |
          v                                     v
[Send M-Pesa STK Push / Card Request] ---> [Pass payment_uuid as Idempotency Key]
          |
          +-----------------------+
          |                       |
          v (Webhook Received)    v (Duplicate Retry Webhook)
[Verify Signature & UUID]   [Match Existing payment_uuid]
          |                       |
          v                       v
[Is Status AWAITING_PAYMENT?] --(NO)--> [Log Duplicate in evt_payment_logs & Exit 200 OK]
          |
        (YES)
          v
[Update Status: SUCCESS]
          |
          v
[Dispatch Redis Queue: GenerateTicketPdf & SendConfirmationEmail]
```

### Exception Handling & Audit Protocol
Centralized exception handling captures payment processing failures and records full debug context directly to the database audit table:

```php
namespace App\Exceptions;

use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Models\PaymentLog;

class Handler extends ExceptionHandler
{
    public function report(Throwable $exception)
    {
        if ($this->shouldReport($exception)) {
            if (request()->is('api/v1/payments/*')) {
                PaymentLog::create([
                    'payment_id' => request()->input('payment_id'),
                    'log_type'   => 'error', // Raw error capture
                    'payload'    => json_encode([
                        'url'     => request()->fullUrl(),
                        'input'   => request()->all(),
                        'trace'   => $exception->getTraceAsString(),
                    ]),
                    'message'    => $exception->getMessage(),
                ]);
            }
        }

        parent::report($exception);
    }
}
```
