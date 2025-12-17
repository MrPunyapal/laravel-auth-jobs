<?php

declare(strict_types=1);

namespace MrPunyapal\LaravelAuthJobs;

use Illuminate\Routing\Router;
use MrPunyapal\LaravelAuthJobs\Contracts\HasContextKeys;
use MrPunyapal\LaravelAuthJobs\Http\Middleware\AuthenticateJobs;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelAuthJobsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-auth-jobs')
            ->hasConfigFile();
    }

    public function registeringPackage(): void
    {
        $this->app->bind(HasContextKeys::class, function () {
            /** @var class-string<HasContextKeys> $class */
            $class = config('auth-jobs.context_keys', ContextKeys::class);

            return new $class;
        });
    }

    public function bootingPackage(): void
    {
        /** @var Router $router */
        $router = $this->app->make(Router::class);

        /** @var array<int, string> $middlewareGroups */
        $middlewareGroups = config('auth-jobs.middleware_groups', []);

        foreach ($middlewareGroups as $group) {
            $router->pushMiddlewareToGroup(
                $group,
                AuthenticateJobs::class
            );
        }
    }
}
