<?php

declare(strict_types=1);

namespace MrPunyapal\LaravelAuthJobs\Concerns;

use MrPunyapal\LaravelAuthJobs\ContextKeys;

trait ResolvesContextKeys
{
    /**
     * @return class-string<\MrPunyapal\LaravelAuthJobs\Contracts\ContextKeysInterface>
     */
    private function contextKeys(): string
    {
        /** @var class-string<\MrPunyapal\LaravelAuthJobs\Contracts\ContextKeysInterface> */
        return config('auth-jobs.context_keys', ContextKeys::class);
    }
}
