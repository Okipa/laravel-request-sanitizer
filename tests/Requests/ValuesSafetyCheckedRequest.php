<?php

namespace Okipa\LaravelRequestSanitizer\Test\Requests;

use Okipa\LaravelRequestSanitizer\RequestSanitizer;

class ValuesSafetyCheckedRequest extends RequestSanitizer
{
    protected $safetyChecks = [
        'booleanTrue'     => 'boolean',
        'booleanNull'     => 'boolean',
        'booleanNotGiven' => 'boolean',
        'arrayFilled'     => 'array',
        'arrayEmpty'      => 'array',
        'arrayNotGiven'   => 'array',
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
