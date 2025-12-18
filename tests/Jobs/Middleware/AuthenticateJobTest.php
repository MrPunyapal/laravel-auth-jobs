<?php

declare(strict_types=1);

use MrPunyapal\LaravelAuthJobs\ContextKeys;
use MrPunyapal\LaravelAuthJobs\Jobs\Middleware\AuthenticateJob;

it('logins the user in the job', function (): void {
    Context::addHidden(ContextKeys::authIdKey(), 1);
    Context::addHidden(ContextKeys::authGuardKey(), 'web');

    Auth::shouldReceive('guard')
        ->once()
        ->with('web')
        ->andReturnSelf();

    Auth::shouldReceive('onceUsingId')
        ->once()
        ->with(1)
        ->andReturn(true);

    $middleware = new AuthenticateJob;

    $job = new class
    {
        public function handle(): bool
        {
            return true;
        }
    };
    $next = (fn ($job) => $job->handle());

    $middleware->handle($job, $next);
});

it('does not login the user in the job', function (): void {
    Context::addHidden(ContextKeys::authIdKey(), null);
    Context::addHidden(ContextKeys::authGuardKey(), null);

    Auth::shouldReceive('guard')->never();
    Auth::shouldReceive('onceUsingId')->never();

    $middleware = new AuthenticateJob;

    $job = new class
    {
        public function handle(): bool
        {
            return true;
        }
    };
    $next = (fn ($job) => $job->handle());

    $middleware->handle($job, $next);
});
