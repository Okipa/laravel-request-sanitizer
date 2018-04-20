# PACKAGE IN DEVELOPMENT

# Laravel Base Request

This Laravel base request is helping you to easily sanitize your request inputs and to manage your inputs controls.

------------------------------------------------------------------------------------------------------------------------

## Installation
- Install the package with composer :
```bash
composer require okipa/laravel-base-request
```
- Laravel 5.5+ uses Package Auto-Discovery, so doesn't require you to manually add the ServiceProvider.
If you don't use auto-discovery or if you use a Laravel 5.4- version, add the package service provider in the `register()` method from your `app/Providers/AppServiceProvider.php` :
```php
// laravel base request
// https://github.com/Okipa/laravel-base-request
$this->app->register(Okipa\LaravelBaseRequest\LaravelBaseRequestServiceProvider::class);
```

------------------------------------------------------------------------------------------------------------------------

## Usage
First, you have to extend the `LaravelBaseRequest` in your `app/Http/Requests/Request.php` class.

```php
class Request extends Okipa\LaravelBaseRequest\ModelJsonStorage
{
    use Okipa\LaravelModelJsonStorage\ModelJsonStorage;

    [...]
}
```
