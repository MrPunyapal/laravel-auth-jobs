<?php

namespace MrPunyapal\LaravelAuthJobs\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Context;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateJobs
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request):Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            Context::addHidden('laravel_auth_jobs_auth_id', Auth::id());
            Context::addHidden('laravel_auth_jobs_auth_guard', Auth::getDefaultDriver());
        }

        return $next($request);
    }
}
