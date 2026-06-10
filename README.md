<p align="center">
<a href="https://packagist.org/packages/aghfatehi/laravel-meta-conversions"><img src="https://img.shields.io/packagist/dt/aghfatehi/laravel-meta-conversions.svg?style=flat-square&label=Downloads" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/aghfatehi/laravel-meta-conversions"><img src="https://img.shields.io/packagist/v/aghfatehi/laravel-meta-conversions.svg?style=flat-square&label=Latest" alt="Latest Version"></a>
<a href="https://packagist.org/packages/aghfatehi/laravel-meta-conversions"><img src="https://img.shields.io/packagist/php-v/aghfatehi/laravel-meta-conversions.svg?style=flat-square" alt="PHP Version"></a>
<a href="https://github.com/aghfatehi/laravel-meta-conversions/actions"><img src="https://img.shields.io/github/actions/workflow/status/aghfatehi/laravel-meta-conversions/tests.yml?style=flat-square&label=Tests" alt="Tests"></a>
<a href="LICENSE"><img src="https://img.shields.io/github/license/aghfatehi/laravel-meta-conversions?style=flat-square" alt="License"></a>
</p>

---

# Laravel Meta Conversions — Meta Pixel & Facebook CAPI Package

**Laravel Meta Conversions** is a complete **Meta Pixel Laravel Package** and **Facebook Pixel Laravel Package** that integrates **Laravel Facebook Pixel** browser tracking with **Laravel Meta Conversions API (CAPI)** server-side tracking. It delivers accurate **Server Side Tracking Laravel** for reliable **Meta Conversion Tracking** and **Facebook Conversion Tracking** across your entire ecommerce funnel.

> **Goal**: High-precision **Laravel Ecommerce Tracking** with event deduplication via unified `event_id` across browser + server for maximum **Meta Advanced Matching** quality. Built for **Laravel Marketing Analytics** and data-driven ad optimization.

---

## Features

### General
- **Laravel Meta Pixel** + CAPI in a single package — full **Laravel CAPI** support
- Laravel 10.x / 11.x / 12.x and PHP 8.2+
- Auto-Discovery (install and run)
- Compatible with official Meta requirements

### Client-side (Laravel Meta Pixel)
- `<x-facebook-conversion::pixel />` — ready-to-use Blade component for **Laravel Facebook Pixel**
- `@include('facebook-conversion::pixel')` — alternative include approach
- Auto `fbq('init')` with your Pixel ID
- Auto `fbq('track', 'PageView')` on every page
- Pass `event_id` from backend to frontend for unified deduplication

### Server-side (Laravel Meta Conversions API)
- Send events via **Laravel Facebook Conversions API** → `graph.facebook.com/{version}/{pixel_id}/events`
- 12 standard events: PageView, ViewContent, Search, AddToWishlist, AddToCart, RemoveFromCart, InitiateCheckout, AddPaymentInfo, **Meta Purchase Event**, Lead, CompleteRegistration, Contact
- Fluent API: `FacebookConversion::event()->name('...')->value(...)->send()`

### User Data & Meta Advanced Matching
- Automatic SHA-256 hashing as required by **Meta Conversion Tracking**
- `em`, `ph`, `fn`, `ln`, `ct`, `st`, `zp`, `country`, `external_id`
- Auto-extract `client_ip_address` and `client_user_agent`
- Auto-read `_fbp`, `_fbc` cookies
- Auto-fill from `auth()->user()` when available

### Queue & Performance
- Queue support via `FACEBOOK_QUEUE=true` for non-blocking **Server Side Tracking Laravel**
- Events dispatched through `SendFacebookEventJob`
- Auto-retry on failure: 3 attempts with 100ms delay
- Configurable HTTP timeout

### Testing & Logging
- Artisan command: `php artisan facebook:test-event` to verify **Laravel CAPI** integration
- Test Event Code support for validation in **Meta Events Manager**
- Dedicated `facebook_conversion` log channel with 30-day rotation
- Access token is masked in all logs

### Security & Error Handling
- Master switch: `FACEBOOK_ENABLED=false` disables all tracking
- Graceful failure — never breaks your application
- Configuration validation before sending
- GDPR-compliant: easy opt-out

---

## Installation

### Step 1: Install the package

```bash
composer require aghfatehi/laravel-meta-conversions
```

> Laravel auto-discovers the Service Provider. If you disable auto-discovery, register manually:

```php
// config/app.php
'providers' => [
    Aghfatehi\LaravelMetaConversions\FacebookConversionServiceProvider::class,
],
'aliases' => [
    'FacebookConversion' => Aghfatehi\LaravelMetaConversions\Facades\FacebookConversion::class,
],
```

### Step 2: Publish config

```bash
php artisan vendor:publish --tag=facebook-conversion-config
```

Config file published to `config/facebook-conversion-service.php`.

### Step 3: Publish views (optional)

```bash
php artisan vendor:publish --tag=facebook-conversion-views
```

Views published to `resources/views/vendor/facebook-conversion/pixel.blade.php`.

---

## Setup

### 1. Get your Facebook Pixel ID

1. Open [Facebook Events Manager → **Meta Events Manager**](https://business.facebook.com/events_manager)
2. Select an existing pixel or create a new one for **Laravel Meta Pixel**
3. Copy the **Pixel ID** from the top
4. Set in your `.env`:

```env
FACEBOOK_PIXEL_ID=123456789012345
```

### 2. Get your CAPI Access Token

1. In **Meta Events Manager** → **Settings**
2. Scroll to **Conversion API**
3. Click **Generate Access Token**
4. Copy the token (starts with `EAA...`)
5. Set in your `.env`:

```env
FACEBOOK_PIXEL_API=EAAJjmXXfkp8BAMZCDLWDZB6ZCk4yUmMbITZBk...
```

> Tokens may expire. Regenerate periodically. Enable domain verification in Business Manager.

### 3. Configure additional variables

```env
# ─── Core ───
FACEBOOK_PIXEL_ID=123456789012345       # Required
FACEBOOK_PIXEL_API=EAA...               # Required
FACEBOOK_ENABLED=true                   # Master toggle

# ─── Testing ───
FACEBOOK_TEST_EVENT_CODE=TEST12345      # Test code (dev only)

# ─── Performance ───
FACEBOOK_QUEUE=true                     # Enable queue (recommended in production)
FACEBOOK_QUEUE_CONNECTION=redis         # Queue connection (optional)

# ─── Logging ───
FACEBOOK_LOGGING=true                   # Enable logging
FACEBOOK_LOG_CHANNEL=facebook_conversion

# ─── API ───
FACEBOOK_API_VERSION=v21.0              # Graph API version
FACEBOOK_TIMEOUT=5                      # HTTP timeout (seconds)
FACEBOOK_RETRY_ATTEMPTS=3               # Retry count
FACEBOOK_RETRY_DELAY=100                # Retry delay (ms)
```

---

## Usage

### Add Pixel to your layout

Insert this in your `<head>` tag:

```blade
{{-- === [Laravel Meta Pixel + CAPI] === --}}
<x-facebook-conversion::pixel />
{{-- =================================== --}}
```

Or using `@include`:

```blade
@include('facebook-conversion::pixel')
```

> The pixel automatically fires `fbq('track', 'PageView')` on every page load.

### Real-world Examples from an Ecommerce Store

#### View Content (Product Page)

Use in `HomeController@product` when a visitor views a product page:

```php
// When a visitor lands on a product page — triggers Laravel Ecommerce Tracking
FacebookConversion::viewContent(
    productId: $product->id,
    productName: $product->name,
    category: $product->category->name,
    value: $product->price,
    currency: 'SAR'
);
```

#### Add to Cart

Use in `CartController@addToCart` after adding a product:

```php
// When a visitor adds a product to cart
FacebookConversion::addToCart(
    productId: $product->id,
    productName: $product->name,
    quantity: $request->quantity,
    value: $price,
    currency: 'SAR'
);
```

#### Add to Wishlist

Use in `WishlistController@store`:

```php
// When a visitor adds a product to wishlist
FacebookConversion::addToWishlist(
    productId: $product->id,
    productName: $product->name,
    value: $product->price,
    currency: 'SAR'
);
```

#### Purchase (most important event)

Use in `OrderController@store` after order creation:

```php
// When a visitor completes a purchase — the Meta Purchase Event
FacebookConversion::purchase(
    value: $order->grand_total,
    currency: 'SAR',
    productIds: $order->orderDetails->pluck('product_id')->toArray()
);
```

### Complete Event List

```php
// 1. PageView
FacebookConversion::pageView();

// 2. ViewContent
FacebookConversion::viewContent(
    productId: 100,
    productName: 'iPhone 15',
    category: 'Phones',
    value: 999,
    currency: 'SAR'
);

// 3. Search
FacebookConversion::search('iphone 15');

// 4. AddToWishlist
FacebookConversion::addToWishlist(
    productId: 100,
    productName: 'iPhone 15',
    value: 999,
    currency: 'SAR'
);

// 5. AddToCart
FacebookConversion::addToCart(
    productId: 100,
    productName: 'iPhone 15',
    quantity: 1,
    value: 999,
    currency: 'SAR'
);

// 6. RemoveFromCart
FacebookConversion::removeFromCart(
    productId: 100,
    productName: 'iPhone 15',
    quantity: 1,
    value: 999,
    currency: 'SAR'
);

// 7. InitiateCheckout
FacebookConversion::initiateCheckout(
    value: 999,
    currency: 'SAR',
    productIds: ['100', '101']
);

// 8. AddPaymentInfo
FacebookConversion::addPaymentInfo(
    value: 999,
    currency: 'SAR'
);

// 9. Purchase (the Meta Purchase Event — highest priority)
FacebookConversion::purchase(
    value: 999,
    currency: 'SAR',
    productIds: ['100', '101']
);

// 10. Lead
FacebookConversion::lead(
    value: 50,
    currency: 'USD'
);

// 11. CompleteRegistration
FacebookConversion::completeRegistration(method: 'email');

// 12. Contact
FacebookConversion::contact();
```

---

## Event Builder (Fluent API)

For full control over your **Laravel CAPI** event payload:

```php
FacebookConversion::event()
    ->name('Purchase')
    ->value(100)
    ->currency('SAR')
    ->contentIds(['product-1', 'product-2'])
    ->contentType('product')
    ->eventId('custom-id-123')
    ->send();
```

Or build the event object and send it later:

```php
$event = FacebookConversion::event()
    ->name('ViewContent')
    ->value(599)
    ->currency('SAR')
    ->build();

// ... do some processing ...

FacebookConversion::sendEvent($event);
```

---

## Event Deduplication — Core of Meta Conversion Tracking

Meta requires a unified `event_id` sent from both **Laravel Facebook Pixel** (browser) and **Laravel CAPI** (server) to identify duplicate events. This is the foundation of accurate **Facebook Conversion Tracking**.

### How it works

```
Single event (e.g. Purchase)
       │
       ├── Browser: fbq('track', 'Purchase', {...}, { eventID: 'uuid-123' })
       │
       └── Server:  CAPI → { "event_name": "Purchase", "event_id": "uuid-123", ... }
       
Meta compares (event_id + event_name) and removes the duplicate
```

The package generates a UUID v4 automatically for every event. You can customize it:

```php
// Custom event_id
FacebookConversion::event()
    ->name('Purchase')
    ->eventId('order_456_' . time())
    ->send();
```

To pass `event_id` from server to Pixel (e.g., from an API response):

```blade
<x-facebook-conversion::pixel
    event-name="Purchase"
    event-id="{{ $eventId }}"
    :custom-data="['value' => 999, 'currency' => 'SAR']"
/>
```

---

## User Data with Meta Advanced Matching

### Automatic Data Collection — for Meta Advanced Matching

Whenever you fire an event, the package automatically collects user data to enhance **Meta Events Manager** with **Meta Advanced Matching**:

```php
// Auto-collected data for better Server Side Tracking Laravel
// 1. From auth()->user() → email, phone, name, last_name
// 2. From request() → ip, user_agent
// 3. From cookies → _fbp, _fbc
```

### SHA-256 Hashing per Meta Requirements

All data is hashed with SHA-256 as required by **Meta Conversion Tracking**:

```php
// Pass custom UserData for better Facebook Conversion Tracking:
$userData = new UserData()
    ->email('user@example.com')
    ->phone('+966551234567')
    ->firstName('Ahmed')
    ->lastName('Ghfatehi')
    ->city('Riyadh')
    ->state('Riyadh Province')
    ->zip('12345')
    ->country('SA')
    ->fbp(request()->cookie('_fbp'))
    ->fbc(request()->cookie('_fbc'));

FacebookConversion::purchase(
    value: 999,
    currency: 'SAR',
    userData: $userData
);
```

### Hashing Reference

| Field | Original | Hashed |
|---|---|---|
| `em` | `user@example.com` | `SHA-256(lowercase(trim(email)))` |
| `ph` | `+966 55 123 4567` | `SHA-256(strip non-numeric)` |
| `fn` | `Ahmed` | `SHA-256(lowercase(trim))` |
| `ln` | `Ghfatehi` | `SHA-256(lowercase(trim))` |
| `ct` | `Riyadh` | `SHA-256(lowercase(trim))` |
| `st` | `Riyadh Province` | `SHA-256(lowercase(trim))` |
| `zp` | `12345` | `SHA-256(lowercase(trim))` |
| `country` | `SA` | `SHA-256(lowercase(trim))` |
| `external_id` | `user_123` | `SHA-256` |

---

## Queue Support

In production, enable the queue to avoid blocking HTTP requests when using **Laravel CAPI**:

```env
FACEBOOK_QUEUE=true
FACEBOOK_QUEUE_CONNECTION=redis
```

Make sure a queue worker is running:

```bash
php artisan queue:work
```

How it works with `FACEBOOK_QUEUE=true`:

```
Controller → FacebookConversion::purchase(...)
                  ↓
            SendFacebookEventJob (dispatch)
                  ↓
            Queue Worker → MetaConversionApiClient → Facebook CAPI
```

The controller returns immediately. The event is processed in the background.

---

## Middleware

### TrackFacebookPageView

Automatically sends `PageView` via **Server Side Tracking Laravel** on every GET request:

```php
// App\Http\Kernel.php
protected $middlewareGroups = [
    'web' => [
        \Aghfatehi\LaravelMetaConversions\Http\Middleware\TrackFacebookPageView::class,
    ],
];
```

Or on specific routes:

```php
Route::middleware('track-facebook-pageview')->group(function () {
    Route::get('/', [HomeController::class, 'index']);
});
```

> The middleware sends the PageView after the response is sent (terminate method) to avoid slowing down the page.

---

## Testing Events via Meta Events Manager

### 1. Using Test Event Code

In development, use Meta's test code to validate your **Laravel CAPI** integration:

```env
FACEBOOK_TEST_EVENT_CODE=TEST12345
```

Open **Meta Events Manager** → **Test Events** and watch your **Laravel Meta Conversions API** events arrive in real time.

### 2. Using Artisan Command

```bash
# Send a test PageView event
php artisan facebook:test-event

# Send a Purchase event with value
php artisan facebook:test-event "Purchase" --value=99.99 --currency=SAR

# Send a ViewContent event
php artisan facebook:test-event "ViewContent" --value=599 --currency=SAR
```

### 3. Verify in Meta Events Manager

After sending events:
1. Open [Meta Events Manager → **Meta Events Manager**](https://business.facebook.com/events_manager)
2. Go to **Test Events** tab (if using Test Event Code)
3. Or go to **Diagnostics** to see real events
4. Check **Event Match Quality** for each event

---

## Logging

Logs are written to `storage/logs/facebook-conversion.log` with daily rotation (30 days).

```log
[2026-06-10 12:00:00] facebook_conversion.INFO: Sending event to Facebook Conversion API {"event_name":"Purchase","event_id":"uuid-123"}
[2026-06-10 12:00:01] facebook_conversion.INFO: Facebook Conversion API event sent successfully {"response":{"events_received":1}}
[2026-06-10 12:00:02] facebook_conversion.ERROR: Facebook Conversion API request failed {"status":401,"response":{"error":{"message":"Invalid token"}}}
```

> Access token is masked: `EAAJjm****`

---

## Production Checklist

- [ ] `FACEBOOK_PIXEL_ID` and `FACEBOOK_PIXEL_API` are set
- [ ] `FACEBOOK_ENABLED=true`
- [ ] `FACEBOOK_TEST_EVENT_CODE` is **empty** (remove in production!)
- [ ] `FACEBOOK_QUEUE=true` and queue is running
- [ ] `php artisan queue:work` (or Supervisor) is active
- [ ] Domain is verified in Facebook Business Manager
- [ ] **Laravel Facebook Pixel** fires on all pages
- [ ] Events appear in **Meta Events Manager**
- [ ] Event Match Quality > 90%

---

## Troubleshooting

| Problem | Solution |
|---|---|
| Events not appearing | Check `FACEBOOK_ENABLED=true` and valid Pixel ID |
| 401 Unauthorized | Regenerate Access Token in **Meta Events Manager** |
| Low match quality | Send more user data (email, phone, fbp, fbc) for **Meta Advanced Matching** |
| Duplicate events | Ensure `event_id` is unique per event (package generates UUID automatically) |
| Queue not working | Check `php artisan queue:work` and queue connection |
| Config error | Check `storage/logs/facebook-conversion.log` |
| Token expired | Regenerate in **Meta Events Manager** → Settings → Conversion API |

---

## Testing

```bash
composer test
```

```text
Tests:    50 passed (113 assertions)
Duration: 2.50s
```

---

## Architecture

```
Controller / Middleware / Artisan
         │
         ▼
FacebookConversionService  ←  Interface (Contract)
         │
         ├── EventBuilder (Fluent API)
         │
         ├── SendFacebookEventJob (Queue)
         │
         └── MetaConversionApiClient (HTTP)
                    │
                    ▼
         Facebook Graph API (CAPI)
```

### Data flow:

```
[User] → [Browser Pixel] ─── fbq('track', ..., {eventID}) ──┐
                                                              ├── → [Meta] ← Dedup
         ↓ [Server] → [Service] → [CAPI] ─── {event_id: same} ──┘
```

---

## License

MIT License. See [LICENSE](LICENSE) for details.

---

<p align="center">
    <sub>Built by <a href="https://fsoftdev.com">Ahmed Ghfatehi</a> · <a href="https://fsoftdev.com">fsoftdev.com</a></sub>
</p>
