<?php

declare(strict_types=1);

namespace MrPunyapal\LaravelAuthJobs;

use MrPunyapal\LaravelAuthJobs\Contracts\HasContextKeys;

final class ContextKeys implements HasContextKeys
{
    public static function authIdKey(): string
    {
        return 'laravel_auth_jobs_auth_id';
    }

    public static function authGuardKey(): string
    {
        return 'laravel_auth_jobs_auth_guard';
    }
}
