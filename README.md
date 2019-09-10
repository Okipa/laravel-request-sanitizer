# Easily sanitize your request inputs

[![Source Code](https://img.shields.io/badge/source-okipa/php--data--sanitizer-blue.svg)](https://github.com/Okipa/laravel-request-sanitizer)
[![Latest Version](https://img.shields.io/github/release/okipa/laravel-request-sanitizer.svg?style=flat-square)](https://github.com/Okipa/laravel-request-sanitizer/releases)
[![Total Downloads](https://img.shields.io/packagist/dt/okipa/laravel-request-sanitizer.svg?style=flat-square)](https://packagist.org/packages/okipa/laravel-request-sanitizer)
[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](https://opensource.org/licenses/MIT)
[![Build Status](https://scrutinizer-ci.com/g/Okipa/laravel-request-sanitizer/badges/build.png?b=master)](https://scrutinizer-ci.com/g/Okipa/laravel-request-sanitizer/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/Okipa/laravel-request-sanitizer/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Okipa/laravel-request-sanitizer/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Okipa/laravel-request-sanitizer/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Okipa/laravel-request-sanitizer/?branch=master)

Easily sanitize your request inputs :
- entries sanitizing ([PHP Input Sanitizer package](https://github.com/ACID-Solutions/input-sanitizer)).
- null entries exclusion.
- values safety check.

## Compatibility

| Laravel version | PHP version | Package version |
|---|---|---|
| ^5.5 | ^7.2 | ^1.1 |
| ^5.0 | ^7.0 | ^1.0 |

## Table of Contents
- [Installation](#installation)
- [Usage](#usage)
- [API](#api)
  - [Properties](#properties)
  - [Public methods](#public-methods)
- [Testing](#testing)
- [Changelog](#changelog)
- [Contributing](#contributing)
- [Credits](#credits)
- [Licence](#license)

## Installation

- Install the package with composer :
```bash
composer require okipa/laravel-request-sanitizer
```

- Extends the `Okipa\LaravelRequestSanitizer\RequestSanitizer` in your `app/Http/Requests/Request.php` class.

```php
<?php

use Okipa\LaravelRequestSanitizer\RequestSanitizer;

class Request extends RequestSanitizer
{
    // your laravel project base request custom features.
}
```

## Usage

```php
<?php

namespace App\Http\Requests;

use Okipa\LaravelRequestSanitizer\RequestSanitizer;

class EditUserRequest extends RequestSanitizer
{
    protected $sanitizeEntries = true; // default value
    protected $exceptFromSanitize = ['user.phone_number']; // except the phone number from the sanitizing treatment in order to keep the phone number first zero (example : 0240506070)
    protected $excludeNullEntries = true; // default value
    protected $exceptFromNullExclusion = ['user.company_name']; // is kept in the request keys even if its value is null
    protected $safetyChecks = ['user.newsletter.subscription' => 'boolean', 'user.permissions' => 'array']; // will make sure that the declared keys will be returned with a default value if not found in the request

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
            'user.newsletter.subscription'  => 'required|boolean',
            'user.permission'               => 'required|array',
            'formatted_date'                => 'required|date|format:Y-m-d H:i:s'
        ];
    }
}
```

## API

### Properties

- `protected $sanitizeEntries = true`
    > Recursively sanitize the request entries.  
    > To check how data will be sanitized, check the used package : https://github.com/Okipa/php-data-sanitizer.  
    > Declare this property to false to disable the request entries sanitizing.
- `protected $exceptFromSanitize = []`
    > Except the declared keys (dot notation accepted) from the request entries sanitizing.  
    > It can be a good option when you have numbers beginning with a zero that you want to keep that way, for example.
- `protected $excludeNullEntries = true`
    > Recursively exclude all the null entries from the request.  
    > Declare this property to false to disable the null entries exclusion.
- `protected $exceptFromNullExclusion = []`
    > Except the declared keys (dot notation accepted) from the null entries exclusion.
- `protected $safetyChecks = []`
    > Set which request keys (dot notation accepted) should be safety checked, according to their types.  
    > Use case : `protected $safetyChecks = ['active' => 'boolean', 'permissions' => 'array'];`.  
    > Accepted types values : `boolean` / `array`.  
    > The keys declared in this array will take the following values (according to their declared types) if they are not found in the request :  
    > - boolean : `false`
    > - array: `[]`

### Public methods

- `before()`
    > This package gives you the opportunity to declare this method in your request.  
    > It will be executed before all the request attributes treatments.

## Testing

``` bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Arthur LORENT](https://github.com/okipa)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.