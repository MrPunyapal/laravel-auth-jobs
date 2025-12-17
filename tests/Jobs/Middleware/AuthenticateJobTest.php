<?php

declare(strict_types=1);

use MrPunyapal\LaravelAuthJobs\Jobs\Middleware\AuthenticateJob;

it('logins the user in the job', function (): void {
    Context::addHidden('laravel_auth_jobs_auth_id', 1);
    Context::addHidden('laravel_auth_jobs_auth_guard', 'web');

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
    Context::addHidden('laravel_auth_jobs_auth_id', null);
    Context::addHidden('laravel_auth_jobs_auth_guard', null);

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
