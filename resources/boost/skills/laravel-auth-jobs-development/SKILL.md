---
name: laravel-auth-jobs-development
description: "Use for tasks involving mrpunyapal/laravel-auth-jobs, authenticated queued jobs, auth context propagation, AuthenticateJob middleware, HasContextKeys customization, and debugging missing auth()->user() inside queue workers. Do not use for generic queue performance tuning or stateless job processing."
license: MIT
metadata:
  author: mrpunyapal
---

# Laravel Auth Jobs Development

Use this skill when a queued job must access the authenticated user that dispatched it. This package captures the authenticated user ID and guard during the HTTP request and restores that auth state inside queued jobs.

## Install and Publish Configuration

```bash
composer require mrpunyapal/laravel-auth-jobs
php artisan vendor:publish --tag="auth-jobs-config"
```

## Required Workflow

1. Dispatch the job from a request handled by one of the middleware groups listed in `config/auth-jobs.php`.
2. Let the package's `AuthenticateJobs` HTTP middleware store the authenticated user ID and guard in Laravel context for that request.
3. Add `new AuthenticateJob` to the queued job's `middleware()` method so the auth state is restored before `handle()` runs.
4. Read `auth()->user()` or run authorization checks inside `handle()` after the middleware has restored the auth state.
5. If your application needs different context keys, replace `context_keys` with a custom class that implements `HasContextKeys`, or bind the interface in a service provider.

The package service provider automatically pushes `AuthenticateJobs` onto the configured middleware groups during package boot, so you do not need to register that middleware manually when the config is correct.

## Job Example

```php
use App\Models\Example;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Gate;
use MrPunyapal\LaravelAuthJobs\Jobs\Middleware\AuthenticateJob;

class ExampleJob implements ShouldQueue
{
    use Queueable;

    public function middleware(): array
    {
        return [new AuthenticateJob];
    }

    public function handle(): void
    {
        $user = auth()->user();

        if ($user === null) {
            return;
        }

        Gate::authorize('view', Example::class);
    }
}
```

## Custom Context Keys

```php
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

Point `context_keys` in `config/auth-jobs.php` to `\App\Auth\CustomContextKeys::class`, or bind `HasContextKeys` to your implementation in a service provider.

## Troubleshooting

- If `auth()->user()` is `null` inside the job, confirm the dispatching request used a configured middleware group and the request was authenticated.
- Confirm the job defines `middleware()` and returns `AuthenticateJob`.
- If you replace context keys, verify the custom class implements `HasContextKeys` and the `context_keys` config points to it.
- Re-run `composer test` after changing middleware or context integration.
