# Configuration

# Configuration

The package ships a small config file at `config/auth-jobs.php`.

## Publishing the config

```bash
php artisan vendor:publish --tag="auth-jobs-config"
```

## Contents

```php
<?php

use MrPunyapal\LaravelAuthJobs\ContextKeys;

return [

    // the middleware groups that are dispatching the jobs which need authentication
    'middleware_groups' => [
        'web',
        // 'api',
    ],

    // the class that provides context keys for storing auth data
    // must implement MrPunyapal\LaravelAuthJobs\Contracts\HasContextKeys
    'context_keys' => ContextKeys::class,
];
```

## `middleware_groups`

The HTTP middleware groups that run `AuthenticateJobs` before dispatching jobs. When the middleware runs it captures the current authenticated user into hidden context, so any job queued from those requests can restore the user later.

Add any group that dispatches jobs needing the authenticated user:

```php
'middleware_groups' => [
    'web',
    'api',
],
```

## `context_keys`

The class used to generate the hidden context keys. By default it is `MrPunyapal\LaravelAuthJobs\ContextKeys`, which uses:

| Key | Purpose |
| --- | --- |
| `laravel_auth_jobs_auth_id` | The authenticated user's ID |
| `laravel_auth_jobs_auth_guard` | The authentication guard name |

If you need different keys — for example to avoid colliding with another package — create your own class implementing `HasContextKeys` and point the config at it. See [Customizing context keys](custom-context-keys/).


---

# Customizing Context Keys

# Customizing Context Keys

By default the package stores the authenticated user's ID and guard under fixed hidden context keys. If another package (or your own code) uses the same keys, you can define your own.

## Implement `HasContextKeys`

Create a class that implements `MrPunyapal\LaravelAuthJobs\Contracts\HasContextKeys`:

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

## Point the config at it

```php
// config/auth-jobs.php
'context_keys' => \App\Auth\CustomContextKeys::class,
```

## Or bind it in a service provider

Instead of the config you can bind your implementation directly in a service provider:

```php
use App\Auth\CustomContextKeys;
use MrPunyapal\LaravelAuthJobs\Contracts\HasContextKeys;

$this->app->bind(HasContextKeys::class, CustomContextKeys::class);
```

Both approaches let the HTTP and job middlewares resolve your implementation through the container.


---

# Installation

# Installation

## Requirements

- PHP `^8.2`, `^8.3`, `^8.4`, or `^8.5`
- Laravel 11, 12, or 13

## Install the package

```bash
composer require mrpunyapal/laravel-auth-jobs
```

The service provider is registered automatically through Laravel's package discovery — no manual registration needed.

## Publish the config file

Publishing the config is optional (sensible defaults are used otherwise). To publish it:

```bash
php artisan vendor:publish --tag="auth-jobs-config"
```

This copies the package config to `config/auth-jobs.php`.

## Laravel Boost

The package ships a Laravel Boost skill that helps AI agents wire `AuthenticateJob`, verify middleware group configuration, customize `HasContextKeys`, and debug missing auth context inside queued jobs.

In a Laravel application that has `laravel/boost` installed, add this package and then discover or refresh the packaged skill:

```bash
php artisan boost:install
php artisan boost:update --discover
```

## Verify the installation

Register the HTTP middleware for the groups in the config and confirm it is applied:

```bash
php artisan about
```

Then dispatch a queued job and confirm `auth()->user()` is resolved inside it. See [Usage](usage/) for a full example.


---

# Laravel Auth Jobs

# Laravel Auth Jobs

Laravel queue jobs run in a separate process, so `auth()->user()` is not available inside them. This package bridges that gap: it captures the authenticated user at request time and restores them right before the job's `handle()` method runs.

## How it works

1. **Capture** — the `AuthenticateJobs` HTTP middleware stores the authenticated user's ID and guard in Laravel's hidden context during the request.
2. **Serialize** — hidden context is automatically serialized together with every queued job.
3. **Restore** — the `AuthenticateJob` job middleware re-authenticates the user (`Auth::guard($guard)->onceUsingId($id)`) before the job processes.

## Features

- Access `auth()->user()` and `Gate::authorize()` inside queued jobs
- Both middlewares ship with the package — no manual wiring required
- Middleware groups are configurable (`web`, `api`, ...)
- Custom context keys via the `HasContextKeys` contract to avoid key collisions with other packages
- Ships an optional Laravel Boost skill for AI agents

## Use cases

- **Authorization** — authorize actions in a job based on the authenticated user's permissions
- **User context** — read the user's data to perform user-specific operations
- **Role-based processing** — branch job logic on the user's roles or permissions
- **Personalization** — apply user preferences while the job runs
- **Audit trail** — log the acting user for traceability and accountability

## Next steps

- [Installation](installation/) — install the package in your Laravel app
- [Configuration](configuration/) — middleware groups and context keys
- [Usage](usage/) — jobs that can see the authenticated user
- [Customizing context keys](custom-context-keys/) — avoid key collisions


---

# Usage

# Usage

The package provides two middleware that work together:

| Middleware | Runs | What it does |
| --- | --- | --- |
| `MrPunyapal\LaravelAuthJobs\Http\Middleware\AuthenticateJobs` | HTTP request (web, api, ...) | Stores the authenticated user's ID and guard in hidden context |
| `MrPunyapal\LaravelAuthJobs\Jobs\Middleware\AuthenticateJob` | Before the job's `handle()` | Restores the user with `Auth::guard(...)->onceUsingId(...)` |

The HTTP middleware is registered automatically for the groups in `middleware_groups` (see [Configuration](configuration/)). Hidden context is serialized with the queued job, so all you need to do is attach the job middleware.

## Job class

```php
<?php

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

    public function handle(): void
    {
        // You can now access auth()->user() here
        $user = auth()->user();

        // authorize your actions
        Gate::authorize('view', Example::class);
    }
}
```

That's it — when the job runs, the authenticated user from the dispatching request is available inside `handle()`.

## How it works under the hood

1. `AuthenticateJobs` (HTTP) checks `Auth::check()` and, when a user is logged in, adds the user ID and the default guard to hidden context with `Context::addHidden(...)`.
2. Laravel serializes hidden context together with the queued job.
3. `AuthenticateJob` (job) reads the hidden values. When both are present it calls `Auth::guard($guard)->onceUsingId($id)` so `auth()->user()` resolves to the original user, then continues the pipeline.

If no auth context was captured (for example the job was queued from an unauthenticated context or from the command line), the middleware simply skips restoration and the job runs as normal — nothing breaks.

