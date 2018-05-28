<?php

namespace Okipa\LaravelRequestSanitizer\Test\Requests;

use Okipa\LaravelRequestSanitizer\RequestSanitizer;

class BeforeSanitizingRequest extends RequestSanitizer
{
    public $dataBeforeTreatment;
    /**
     * Execute some treatments just after the request creation
     */
    public function before()
    {
        $this->dataBeforeTreatment = $this->all();
    }

    /**
     * Set the validation rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }
}
