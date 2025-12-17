<?php

declare(strict_types=1);

namespace MrPunyapal\LaravelAuthJobs\Contracts;

interface ContextKeys
{
    /**
     * Get the context key for storing the authenticated user's ID.
     */
    public static function authIdKey(): string;

    /**
     * Get the context key for storing the authentication guard name.
     */
    public static function authGuardKey(): string;
}
