# This package allows you to access the authenticated user while processing jobs in the queue.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mrpunyapal/laravel-auth-jobs.svg?style=flat-square)](https://packagist.org/packages/mrpunyapal/laravel-auth-jobs)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/mrpunyapal/laravel-auth-jobs/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/mrpunyapal/laravel-auth-jobs/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/mrpunyapal/laravel-auth-jobs.svg?style=flat-square)](https://packagist.org/packages/mrpunyapal/laravel-auth-jobs)

## Installation

You can install the package via composer:

```bash
composer require mrpunyapal/laravel-auth-jobs
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="auth-jobs-config"
```

This is the contents of the published config file:

```php
return [
    // the middleware groups that are dispatching the jobs which need authentication
    'middleware_groups' => [
        'web',
        // 'api',
    ],

    // the class that provides context keys for storing auth data
    // must implement MrPunyapal\LaravelAuthJobs\Contracts\HasContextKeys
    'context_keys' => \MrPunyapal\LaravelAuthJobs\ContextKeys::class,
];
```

## Usage

This package provides two middleware: `AuthenticateJob`, you can add this middleware to your job class. and you can now access `auth()->user()` in your job class.

### Job Class

```php
use App\Models\Example;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use MrPunyapal\LaravelAuthJobs\Jobs\Middleware\AuthenticateJob;

class ExampleJob implements ShouldQueue
{
    use Queueable;

    /**
     * Get the middleware the job should pass through.
     *
     * @return array<int, object>
     */
    public function middleware(): array
    {
        return [new AuthenticateJob];
    }

    public function handle()
    {
        // You can now access auth()->user() here
        $user = auth()->user();

        // authorize your actions
        Gate::authorize('view', Example::class);
    }
}

```

## Customizing Context Keys

If you need to use custom context keys (e.g., to avoid conflicts with other packages), you can create your own class implementing `HasContextKeys`:

```php
<?php

namespace App\Auth;

use MrPunyapal\LaravelAuthJobs\Contracts\HasContextKeys;

final class CustomContextKeys implements HasContextKeys
{
    public static function authIdKey(): string
    {
        return 'my_app_auth_user_id';
    }

    public static function authGuardKey(): string
    {
        return 'my_app_auth_guard';
    }
}
```

Then update your config:

```php
// config/auth-jobs.php
'context_keys' => \App\Auth\CustomContextKeys::class,
```

Alternatively, you can bind your implementation directly in a service provider:

```php
use MrPunyapal\LaravelAuthJobs\Contracts\HasContextKeys;
use App\Auth\CustomContextKeys;

$this->app->bind(HasContextKeys::class, CustomContextKeys::class);
```

## Use Cases

- **Authorization**: Authorize actions in your job class based on the authenticated user's permissions.
- **User Context**: Access the authenticated user's data within jobs to perform user-specific operations.
- **Role-Based Processing**: Execute different logic in your jobs based on the user's roles or permissions.
- **Personalization**: Apply user preferences and settings during job execution for customized processing.
- **Audit Trail**: Create comprehensive audit logs that include user information, improving traceability and accountability.

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

- [Punyapal Shah](https://github.com/MrPunyapal)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
