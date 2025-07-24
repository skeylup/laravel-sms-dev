# 📱 Laravel SMS Dev

[![Latest Version on Packagist](https://img.shields.io/packagist/v/skeylup/laravel-sms-dev.svg?style=flat-square)](https://packagist.org/packages/skeylup/laravel-sms-dev)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/skeylup/laravel-sms-dev/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/skeylup/laravel-sms-dev/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/skeylup/laravel-sms-dev/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/skeylup/laravel-sms-dev/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/skeylup/laravel-sms-dev.svg?style=flat-square)](https://packagist.org/packages/skeylup/laravel-sms-dev)

**A Laravel package for intercepting and debugging SMS messages in development and staging environments.**

Laravel SMS Dev allows you to intercept all SMS messages sent by your Laravel application and display them in an elegant web interface, similar to a mailbox. Perfect for development and testing!

## ✨ Features

- 📨 **Elegant Web Interface** - View all intercepted SMS messages in a mailbox-like interface
- 🔍 **Complete Details** - See content, metadata, recipients, and notification classes
- 📊 **Real-time Statistics** - Counters for sent, unread, read SMS messages
- 🧹 **SMS Management** - Mark as read, delete, clear all messages
- ⚡ **Artisan Commands** - Test, clean, and manage SMS via CLI
- 🎯 **Laravel Notification Channel** - Native integration with Laravel's notification system
- 🔧 **Flexible Configuration** - Enable/disable by environment
- 🔐 **Authorization System** - Telescope-like access control for production environments

## 📦 Installation

Install the package via Composer:

```bash
composer require skeylup/laravel-sms-dev
```

Publish and run the migrations:

```bash
php artisan vendor:publish --provider="Skeylup\LaravelSmsDev\LaravelSmsDevServiceProvider"
php artisan migrate
```

## ⚙️ Configuration

The configuration file `config/sms-dev.php` will be published automatically:

```php
return [
    // Enable the package (default: local/testing/staging environments)
    'enabled' => env('SMS_DEV_ENABLED', in_array(env('APP_ENV'), ['local', 'testing', 'staging'])),

    // Route configuration
    'route' => [
        'prefix' => env('SMS_DEV_ROUTE_PREFIX', 'sms-dev'),
    ],

    // Authorization gate name
    'gate' => env('SMS_DEV_GATE', 'ViewSms'),

    // Middleware stack
    'middleware' => [
        'web',
        'sms-dev-auth',
    ],

    // Auto-cleanup configuration
    'cleanup' => [
        'enabled' => env('SMS_DEV_CLEANUP_ENABLED', true),
        'days' => env('SMS_DEV_CLEANUP_DAYS', 30),
    ],

    // Default sender
    'default_from' => env('SMS_DEV_DEFAULT_FROM', 'SMS-DEV'),

    // Pagination
    'per_page' => env('SMS_DEV_PER_PAGE', 20),
];
```

### Environment Variables

Add these variables to your `.env` file:

```env
# Enable/disable SMS Dev
SMS_DEV_ENABLED=true

# Route prefix (default: sms-dev)
SMS_DEV_ROUTE_PREFIX=sms-dev

# Authorization gate name (default: ViewSms)
SMS_DEV_GATE=ViewSms

# Auto-cleanup settings
SMS_DEV_CLEANUP_ENABLED=true
SMS_DEV_CLEANUP_DAYS=30

# Default sender name
SMS_DEV_DEFAULT_FROM=MyApp

# Pagination
SMS_DEV_PER_PAGE=50
```

## 🔐 Authorization

Laravel SMS Dev includes a Telescope-like authorization system to control access in production environments only.

### 1. Create Authorization Service Provider

Create a service provider to define who can access SMS Dev:

```bash
php artisan make:provider SmsdevServiceProvider
```

### 2. Define the Authorization Gate

```php
<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class SmsdevServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->gate();
    }

    /**
     * Register the SMS Dev gate.
     *
     * This gate determines who can access SMS Dev in production environments.
     */
    protected function gate(): void
    {
        Gate::define('ViewSms', function (?User $user) {
            // Only apply in production environment
            if (!app()->environment('production')) {
                return true;
            }

            // Require authentication in production
            if (!$user) {
                return false;
            }

            // Define authorized users by email
            return in_array($user->email, [
                'admin@example.com',
                'developer@example.com',
                // Add more authorized emails here
            ]);
        });
    }
}
```

### 3. Register the Service Provider

Add the service provider to your `bootstrap/providers.php`:

```php
return [
    // ... other providers
    App\Providers\SmsdevServiceProvider::class,
];
```

### 4. Authorization Behavior

- **Non-Production Environments** (local, testing, staging): Always accessible, no authentication required
- **Production Environment**:
  - Requires authentication (`auth` middleware)
  - Checks the `ViewSms` gate
  - Denies access if gate is not defined or user is not authorized

### 5. Custom Authorization Logic

You can customize the authorization logic in your gate:

```php
Gate::define('ViewSms', function (?User $user) {
    // Only apply in production
    if (!app()->environment('production')) {
        return true;
    }

    // Role-based access
    return $user && $user->hasRole('admin');

    // Permission-based access
    return $user && $user->can('view-sms-dev');

    // Custom logic
    return $user && $user->is_developer && $user->email_verified_at;
});
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [EGGERMONT Kévin](https://github.com/skeylup)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
