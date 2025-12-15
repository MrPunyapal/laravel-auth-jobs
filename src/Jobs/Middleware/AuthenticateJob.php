<?php

namespace MrPunyapal\LaravelAuthJobs\Jobs\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Context;

class AuthenticateJob
{
    /**
     * Process the queued job.
     *
     * @param  Closure(object):void  $next
     */
    public function handle(object $job, Closure $next): void
    {
        $guard = Context::getHidden('laravel_auth_jobs_auth_guard');
        $id = Context::getHidden('laravel_auth_jobs_auth_id');

        if (is_null($guard) || is_null($id)) {
            $next($job);

            return;
        }

        Auth::guard($guard)->onceUsingId($id);

        $next($job);
    }
}
