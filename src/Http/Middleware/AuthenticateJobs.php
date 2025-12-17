<?php

declare(strict_types=1);

namespace MrPunyapal\LaravelAuthJobs\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Context;
use MrPunyapal\LaravelAuthJobs\Concerns\ResolvesContextKeys;
use Symfony\Component\HttpFoundation\Response;

readonly class AuthenticateJobs
{
    use ResolvesContextKeys;

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request):Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $contextKeys = $this->contextKeys();

            Context::addHidden($contextKeys::authIdKey(), Auth::id());
            Context::addHidden($contextKeys::authGuardKey(), Auth::getDefaultDriver());
        }

        return $next($request);
    }
}
