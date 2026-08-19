# Installation

## Requirements

- PHP `^8.3`, `^8.4`, or `^8.5`
- Laravel 12 or 13

## Install the package

```bash
composer require mrpunyapal/laravel-auth-jobs
```

The service provider is registered automatically through Laravel's package discovery — no manual registration needed.

## Publish the config file

Publishing the config is optional (sensible defaults are used otherwise). To publish it:

```bash
php artisan vendor:publish --tag="auth-jobs-config"
```

This copies the package config to `config/auth-jobs.php`.

## Laravel Boost

The package ships a Laravel Boost skill that helps AI agents wire `AuthenticateJob`, verify middleware group configuration, customize `HasContextKeys`, and debug missing auth context inside queued jobs.

In a Laravel application that has `laravel/boost` installed, add this package and then discover or refresh the packaged skill:

```bash
php artisan boost:install
php artisan boost:update --discover
```

## Verify the installation

Register the HTTP middleware for the groups in the config and confirm it is applied:

```bash
php artisan about
```

Then dispatch a queued job and confirm `auth()->user()` is resolved inside it. See [Usage](usage/) for a full example.
