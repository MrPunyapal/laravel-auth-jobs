<?php

declare(strict_types=1);

namespace MrPunyapal\LaravelAuthJobs;

use MrPunyapal\LaravelAuthJobs\Contracts\ContextKeysInterface;

enum ContextKeys: string implements ContextKeysInterface
{
    case AuthId = 'laravel_auth_jobs_auth_id';

    case AuthGuard = 'laravel_auth_jobs_auth_guard';

    public static function authIdKey(): string
    {
        return self::AuthId->value;
    }

    public static function authGuardKey(): string
    {
        return self::AuthGuard->value;
    }
}
