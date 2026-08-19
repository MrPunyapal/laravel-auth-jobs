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
