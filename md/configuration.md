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
