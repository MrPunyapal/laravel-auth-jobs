<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Request;
use MrPunyapal\LaravelAuthJobs\ContextKeys;
use MrPunyapal\LaravelAuthJobs\Http\Middleware\AuthenticateJobs;

it('adds auth_id and auth guard into context', function (): void {
    $middleware = new AuthenticateJobs;

    $request = Request::create('/');

    Auth::shouldReceive('check')
        ->once()
        ->andReturn(true);

    Auth::shouldReceive('id')
        ->once()
        ->andReturn(1);

    Auth::shouldReceive('getDefaultDriver')
        ->once()
        ->andReturn('web');

    $middleware->handle($request, fn () => response()->json(['message' => 'ok']));

    expect(Context::getHidden(ContextKeys::AuthId->value))->toBe(1)
        ->and(Context::getHidden(ContextKeys::AuthGuard->value))->toBe('web');
});

it('does not add auth_id and auth guard into context', function (): void {
    $middleware = new AuthenticateJobs;

    $request = Request::create('/');

    Auth::shouldReceive('check')
        ->once()
        ->andReturn(false);

    $middleware->handle($request, fn () => response()->json(['message' => 'ok']));

    expect(Context::getHidden(ContextKeys::AuthId->value))->toBeNull()
        ->and(Context::getHidden(ContextKeys::AuthGuard->value))->toBeNull();
});
