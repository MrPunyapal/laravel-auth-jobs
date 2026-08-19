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
