# PACKAGE IN DEVELOPMENT

# Laravel Base Request

This package is helping you to easily sanitize your request entries :
- entries auto-casting ([PHP Input Sanitizer package](https://github.com/ACID-Solutions/input-sanitizer)).
- null entries exclusion.
- boolean values safety check.

------------------------------------------------------------------------------------------------------------------------

## Third party packages usage
- This package implements and uses the [PHP Input Sanitizer package](https://github.com/ACID-Solutions/input-sanitizer).

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
$this->app->register(Okipa\LaravelRequestSanitizer\LaravelRequestSanitizerServiceProvider::class);
```

------------------------------------------------------------------------------------------------------------------------

## Usage
First, you have to extend the `Okipa\LaravelRequestSanitizer\RequestSanitizer` in your `app/Http/Requests/Request.php` class.

```php
use Okipa\LaravelRequestSanitizer\RequestSanitizer;

class Request extends RequestSanitizer
{
    // your laravel base request custom features
}
```

------------------------------------------------------------------------------------------------------------------------

## Laravel base request API

### Properties
| Property | Default value | Description |
|-----------|-----------|-----------|
| $sanitizeEntries | true | Recursively sanitize the request entries. To check how data will be sanitized, check the used package : https://github.com/Okipa/php-data-sanitizer. Declare this property to false to disable the request entries sanitizing. |
| $exceptFromSanitize | [] | Except the given keys (dot notation accepted) from the request entries sanitizing. It can be a good option when you have numbers beginning with a zero that you want to keep that way, for example. |
| $excludeNullEntries | true | Recursively exclude all the null entries from the request. Declare this property to false to disable the null entries exclusion. |
| $exceptFromNullExclusion | [] | Except the given keys (dot notation accepted) from the null entries exclusion. |
| $safetyCheckBooleanValues | [] | Set the which request keys (dot notation accepted) should be safety checked. If a given key is declared in this array and is not found in the request, it will take « false » for value. |

### Methods
| Method |  Description |
|-----------|-----------|
| before() | This package gives you the opportunity to declare this method in your request. It will be executed just after the request instantiation and before all other treatments. |
