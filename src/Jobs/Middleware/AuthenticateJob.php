<?php

declare(strict_types=1);

namespace MrPunyapal\LaravelAuthJobs\Jobs\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Context;
use MrPunyapal\LaravelAuthJobs\ContextKeys;

readonly class AuthenticateJob
{
    /**
     * Process the queued job.
     *
     * @param  Closure(object):void  $next
     */
    public function handle(object $job, Closure $next): void
    {
        $guard = Context::getHidden(ContextKeys::AUTH_GUARD);
        $id = Context::getHidden(ContextKeys::AUTH_ID);

        if (is_null($guard) || is_null($id)) {
            $next($job);

            return;
        }

        Auth::guard($guard)->onceUsingId($id);

        $next($job);
    }
}
