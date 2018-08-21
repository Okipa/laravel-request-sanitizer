<?php

namespace Okipa\LaravelRequestSanitizer\Test\Requests;

use Okipa\LaravelRequestSanitizer\RequestSanitizer;

class AuthorizationCheckRequest extends RequestSanitizer
{
    public $sanitizedData;
    
    public function authorize()
    {
        $this->sanitizedData = $this->all();
    }
}
