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
