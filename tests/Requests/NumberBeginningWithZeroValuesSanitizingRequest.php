<?php

namespace Okipa\LaravelRequestSanitizer\Test\Requests;

use Okipa\LaravelRequestSanitizer\RequestSanitizer;

class NumberBeginningWithZeroValuesSanitizingRequest extends RequestSanitizer
{
    protected $exceptFromSanitize = ['numberBeginningWithZeroExcepted'];

    /**
     * Execute some treatments just after the request creation
     */
    public function before()
    {
        //
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
