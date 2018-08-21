<?php

namespace Okipa\LaravelRequestSanitizer\Test\Requests;

use Okipa\LaravelRequestSanitizer\RequestSanitizer;

class BeforeSanitizingRequest extends RequestSanitizer
{
    public $dataBeforeTreatment;
 
    public function before()
    {
        $this->dataBeforeTreatment = $this->all();
    }
}
