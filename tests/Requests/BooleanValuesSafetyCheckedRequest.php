<?php

namespace Okipa\LaravelRequestSanitizer\Test\Requests;

use Okipa\LaravelRequestSanitizer\RequestSanitizer;

class BooleanValuesSafetyCheckedRequest extends RequestSanitizer
{
    protected $safetyCheckBooleanValues = [
        'booleanTrue',
        'BooleanNull',
        'BooleanNotGiven'
    ];

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
