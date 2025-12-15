<?php

use Illuminate\Routing\Router;
use MrPunyapal\LaravelAuthJobs\Http\Middleware\AuthenticateJobs;
use MrPunyapal\LaravelAuthJobs\LaravelAuthJobsServiceProvider;

it('adds middleware to configured groups', function (): void {
    $this->app['config']->set('auth-jobs.middleware_groups', ['web', 'abc']);

    $provider = new LaravelAuthJobsServiceProvider($this->app);

    $provider->bootingPackage();

    $router = $this->app->make(Router::class);

    expect($router->getMiddlewareGroups())->toHaveKey('web')
        ->and($router->getMiddlewareGroups()['web'])->toContain(AuthenticateJobs::class)
        ->and($router->getMiddlewareGroups())->toHaveKey('abc')
        ->and($router->getMiddlewareGroups()['abc'])->toContain(AuthenticateJobs::class);
});
