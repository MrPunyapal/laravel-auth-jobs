<?php

use Illuminate\Routing\Router;
use MrPunyapal\LaravelAuthJobs\Http\Middleware\AuthenticateJobs;
use MrPunyapal\LaravelAuthJobs\LaravelAuthJobsServiceProvider;

it('adds middleware to configured groups', function (): void {
    $this->app['config']->set('auth-jobs.middleware_groups', ['web', 'abc']);

    $provider = new LaravelAuthJobsServiceProvider($this->app);

    $provider->bootingPackage();

    $router = $this->app->make(Router::class);

    expect($router->getMiddlewareGroups())->toHaveKey('web');
    expect($router->getMiddlewareGroups()['web'])->toContain(AuthenticateJobs::class);

    expect($router->getMiddlewareGroups())->toHaveKey('abc');
    expect($router->getMiddlewareGroups()['abc'])->toContain(AuthenticateJobs::class);
});
