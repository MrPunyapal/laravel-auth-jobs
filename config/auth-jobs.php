<?php

declare(strict_types=1);

use MrPunyapal\LaravelAuthJobs\ContextKeys;

return [

    // the middleware groups that are dispatching the jobs which need authentication
    'middleware_groups' => [
        'web',
        // 'api',
    ],

    // the enum class that provides context keys for storing auth data
    // must implement MrPunyapal\LaravelAuthJobs\Contracts\ContextKeysInterface
    'context_keys' => ContextKeys::class,
];
