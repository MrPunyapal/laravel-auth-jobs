<?php

declare(strict_types=1);

namespace MrPunyapal\LaravelAuthJobs\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Context;
use MrPunyapal\LaravelAuthJobs\Contracts\ContextKeys as ContextKeysContract;
use Symfony\Component\HttpFoundation\Response;

readonly class AuthenticateJobs
{
    public function __construct(
        private ContextKeysContract $contextKeys
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request):Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            Context::addHidden($this->contextKeys::authIdKey(), Auth::id());
            Context::addHidden($this->contextKeys::authGuardKey(), Auth::getDefaultDriver());
        }

        return $next($request);
    }
}
