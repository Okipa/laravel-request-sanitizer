<?php

namespace Okipa\LaravelRequestSanitizer\Test\Requests;

use Okipa\LaravelRequestSanitizer\RequestSanitizer;

class DisabledNullEntriesExclusionRequest extends RequestSanitizer
{
    protected $excludeNullEntries = false;
    protected $exceptFromNullExclusion = [
        'otherNullEntry',
    ];
}
