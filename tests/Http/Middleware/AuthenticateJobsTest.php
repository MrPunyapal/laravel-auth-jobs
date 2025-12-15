<?php

use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Request;
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

    expect(Context::getHidden('laravel_auth_jobs_auth_id'))->toBe(1);
    expect(Context::getHidden('laravel_auth_jobs_auth_guard'))->toBe('web');
});

it('does not add auth_id and auth guard into context', function (): void {
    $middleware = new AuthenticateJobs;

    $request = Request::create('/');

    Auth::shouldReceive('check')
        ->once()
        ->andReturn(false);

    $middleware->handle($request, fn () => response()->json(['message' => 'ok']));

    expect(Context::getHidden('laravel_auth_jobs_auth_id'))->toBeNull();
    expect(Context::getHidden('laravel_auth_jobs_auth_guard'))->toBeNull();
});
