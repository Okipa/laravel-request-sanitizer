# Laravel Request Sanitizer

[![Source Code](https://img.shields.io/badge/source-okipa/php--data--sanitizer-blue.svg)](https://github.com/Okipa/laravel-request-sanitizer)
[![Latest Version](https://img.shields.io/github/release/okipa/laravel-request-sanitizer.svg?style=flat-square)](https://github.com/Okipa/laravel-request-sanitizer/releases)
[![Total Downloads](https://img.shields.io/packagist/dt/okipa/laravel-request-sanitizer.svg?style=flat-square)](https://packagist.org/packages/okipa/laravel-request-sanitizer)
[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](https://opensource.org/licenses/MIT)
[![Build Status](https://scrutinizer-ci.com/g/Okipa/laravel-request-sanitizer/badges/build.png?b=master)](https://scrutinizer-ci.com/g/Okipa/laravel-request-sanitizer/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/Okipa/laravel-request-sanitizer/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Okipa/laravel-request-sanitizer/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Okipa/laravel-request-sanitizer/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Okipa/laravel-request-sanitizer/?branch=master)

This package is helping you to easily sanitize your request entries :
- entries sanitizing ([PHP Input Sanitizer package](https://github.com/ACID-Solutions/input-sanitizer)).
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
| $exceptFromSanitize | [] | Except the declared keys (dot notation accepted) from the request entries sanitizing. It can be a good option when you have numbers beginning with a zero that you want to keep that way, for example. |
| $excludeNullEntries | true | Recursively exclude all the null entries from the request. Declare this property to false to disable the null entries exclusion. |
| $exceptFromNullExclusion | [] | Except the declared keys (dot notation accepted) from the null entries exclusion. |
| $safetyCheckBooleanValues | [] | Set which request keys associated boolean values (dot notation accepted) should be safety checked. If a given key or its associated boolean value is declared in this array and is not given in the request, it will take « false » for value. |

### Methods
| Method |  Description |
|-----------|-----------|
| before() | This package gives you the opportunity to declare this method in your request. It will be executed just after the request instantiation and before all other treatments. |

------------------------------------------------------------------------------------------------------------------------

## Usage example

```php

namespace App\Http\Requests;

use App\Http\Requests\BaseRequest; // your base request has to extend the RequestSanitizer

class EditUserRequest extends BaseRequest
{
    protected $sanitizeEntries = true; // default value
    protected $exceptFromSanitize = ['user.phone_number']; // except the phone number from the sanitizing treatment in order to keep the phone number first zero (example : 0240506070)
    protected $excludeNullEntries = true; // default value
    protected $exceptFromNullExclusion = ['user.company_name']; // is kept in the request keys even if its value is null
    protected $safetyCheckBooleanValues = ['user.newsletter.subscription','user.activation']; // will make sure that the declared keys will be returned as boolean values in the request (will take « false » as value if not given)

    /**
     * Execute some treatments just after the request creation
     */
    public function before()
    {
        // execute your custom request treatments here
        $this->merge(['formatted_date' => Carbon::createFromFormat('d/m/Y H:i:s', $this->input('user.created_at')->toDateTimeString()]);
    }

    /**
     * Set the validation rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            // other rules ...
            'user.phone_number'             => 'required|string',
            'user.company_name'             => 'nullable|string|max:255',
            'user.newsletter.subscription'  => 'required|boolean'
            'user.activation'               => 'required|boolean',
            'formatted_date'                =>  ''
        ];
    }
}
```
