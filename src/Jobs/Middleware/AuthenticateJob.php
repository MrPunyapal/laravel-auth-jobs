<?php

declare(strict_types=1);

namespace MrPunyapal\LaravelAuthJobs\Jobs\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Context;
use MrPunyapal\LaravelAuthJobs\Contracts\ContextKeys as ContextKeysContract;

readonly class AuthenticateJob
{
    public function __construct(
        private ContextKeysContract $contextKeys
    ) {}

    /**
     * Process the queued job.
     *
     * @param  Closure(object):void  $next
     */
    public function handle(object $job, Closure $next): void
    {
        $guard = Context::getHidden($this->contextKeys::authGuardKey());
        $id = Context::getHidden($this->contextKeys::authIdKey());

        if (! is_string($guard) || is_null($id)) {
            $next($job);

            return;
        }

        Auth::guard($guard)->onceUsingId($id);

        $next($job);
    }
}
