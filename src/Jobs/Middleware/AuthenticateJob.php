<?php

declare(strict_types=1);

namespace MrPunyapal\LaravelAuthJobs\Jobs\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Context;
use MrPunyapal\LaravelAuthJobs\Contracts\HasContextKeys;

readonly class AuthenticateJob
{
    /**
     * Process the queued job.
     *
     * @param  Closure(object):void  $next
     */
    public function handle(object $job, Closure $next): void
    {
        $contextKeys = resolve(HasContextKeys::class);

        $guard = Context::getHidden($contextKeys::authGuardKey());
        $id = Context::getHidden($contextKeys::authIdKey());

        if (! is_string($guard) || is_null($id)) {
            $next($job);

            return;
        }

        Auth::guard($guard)->onceUsingId($id);

        $next($job);
    }
}
