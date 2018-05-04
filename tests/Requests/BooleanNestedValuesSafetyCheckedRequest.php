<?php

namespace Okipa\LaravelRequestSanitizer\Test\Requests;

use Okipa\LaravelRequestSanitizer\RequestSanitizer;

class BooleanNestedValuesSafetyCheckedRequest extends RequestSanitizer
{
    protected $safetyCheckBooleanValues = [
        'user.activatedTrue',
        'user.activatedNotGiven'
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
