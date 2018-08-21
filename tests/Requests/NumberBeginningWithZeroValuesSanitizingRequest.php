<?php

namespace Okipa\LaravelRequestSanitizer\Test\Requests;

use Okipa\LaravelRequestSanitizer\RequestSanitizer;

class NumberBeginningWithZeroValuesSanitizingRequest extends RequestSanitizer
{
    protected $exceptFromSanitize = ['numberBeginningWithZeroExcepted'];
}
