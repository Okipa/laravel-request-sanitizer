<?php

namespace Okipa\LaravelRequestSanitizer\Test\Requests;

use Okipa\LaravelRequestSanitizer\RequestSanitizer;

class NumberBeginningWithZeroNestedValuesSanitizingRequest extends RequestSanitizer
{
    protected $exceptFromSanitize = ['user.numberBeginningWithZeroExcepted'];
}
