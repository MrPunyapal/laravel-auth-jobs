<?php

declare(strict_types=1);

namespace MrPunyapal\LaravelAuthJobs;

enum ContextKeys: string
{
    case AuthId = 'laravel_auth_jobs_auth_id';

    case AuthGuard = 'laravel_auth_jobs_auth_guard';
}
