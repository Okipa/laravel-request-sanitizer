<?php

namespace Okipa\LaravelRequestSanitizer\Test\Requests;

use Okipa\LaravelRequestSanitizer\RequestSanitizer;

class ExceptNullEntryFromNullExclusionRequest extends RequestSanitizer
{
    protected $exceptFromNullExclusion = [
        'otherNullEntry',
    ];
}
