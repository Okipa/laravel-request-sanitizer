<?php

namespace Okipa\LaravelRequestSanitizer\Test\Requests;

use Okipa\LaravelRequestSanitizer\RequestSanitizer;

class DisabledEntriesSanitizingRequest extends RequestSanitizer
{
    protected $sanitizeEntries = false;
    protected $exceptFromSanitize = [
        'numberBeginningWithZeroExcepted',
    ];
}
