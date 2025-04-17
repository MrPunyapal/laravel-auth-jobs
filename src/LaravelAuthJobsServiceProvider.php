<?php

namespace MrPunyapal\LaravelAuthJobs;

use Illuminate\Routing\Router;
use MrPunyapal\LaravelAuthJobs\Http\Middleware\AuthorizeJobs;
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

    public function bootingPackage()
    {
        $router = $this->app->make(Router::class);
        $router->pushMiddlewareToGroup('web', AuthorizeJobs::class);
    }
}
