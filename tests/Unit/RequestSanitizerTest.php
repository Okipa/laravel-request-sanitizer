<?php

namespace Okipa\LaravelRequestSanitizer\Test\Unit;

use Illuminate\Support\Facades\Hash;
use Okipa\LaravelRequestSanitizer\Test\Requests\BeforeSanitizingRequest;
use Okipa\LaravelRequestSanitizer\Test\Requests\BooleanNestedValuesSafetyCheckedRequest;
use Okipa\LaravelRequestSanitizer\Test\Requests\BooleanValuesSafetyCheckedRequest;
use Okipa\LaravelRequestSanitizer\Test\Requests\DisabledEntriesSanitizingRequest;
use Okipa\LaravelRequestSanitizer\Test\Requests\DisabledNullEntriesExclusionRequest;
use Okipa\LaravelRequestSanitizer\Test\Requests\ExceptNullEntryFromNullExclusionRequest;
use Okipa\LaravelRequestSanitizer\Test\Requests\NullEntriesExclusionRequest;
use Okipa\LaravelRequestSanitizer\Test\Requests\NumberBeginningWithZeroNestedValuesSanitizingRequest;
use Okipa\LaravelRequestSanitizer\Test\Requests\NumberBeginningWithZeroValuesSanitizingRequest;
use Okipa\LaravelRequestSanitizer\Test\RequestSanitizerTestCase;

class RequestSanitizerTest extends RequestSanitizerTestCase
{
    public function testRequestSanitizingWithNumberBeginningWithZeroValues()
    {
        $data = [
            'numberBeginningWithZero'         => '0123456',
            'numberBeginningWithZeroExcepted' => '0123456',
        ];
        $request = NumberBeginningWithZeroValuesSanitizingRequest::create('test', 'GET', $data);
        $request->sanitizeRequest();
        $this->assertEquals([
            'numberBeginningWithZero'         => 123456,
            'numberBeginningWithZeroExcepted' => '0123456',
        ], $request->all());
    }

    public function testRequestBefore()
    {
        $data = [
            'numberBeginningWithZero'         => '0123456',
            'numberBeginningWithZeroExcepted' => '0123456',
        ];
        $request = BeforeSanitizingRequest::create('test', 'GET', $data);
        $request->sanitizeRequest();
        $this->assertEquals($data, $request->dataBeforeTreatment);
        $this->assertEquals([
            'numberBeginningWithZero'         => 123456,
            'numberBeginningWithZeroExcepted' => '0123456',
        ], $request->all());
    }

    public function testRequestSanitizingWithNumberBeginningWithZeroNestedValues()
    {
        $data = [
            'user' => [
                'numberBeginningWithZero'         => '0123456',
                'numberBeginningWithZeroExcepted' => '0123456',
            ],
        ];
        $request = NumberBeginningWithZeroNestedValuesSanitizingRequest::create('test', 'GET', $data);
        $request->sanitizeRequest();
        $this->assertEquals([
            'user' => [
                'numberBeginningWithZero'         => 123456,
                'numberBeginningWithZeroExcepted' => '0123456',
            ],
        ], $request->all());
    }

    public function testRequestWithBooleanValues()
    {
        $request = BooleanValuesSafetyCheckedRequest::create('test', 'GET', [
            'booleanTrue' => true,
            'BooleanNull' => null,
        ]);
        $request->sanitizeRequest();
        $this->assertEquals([
            'booleanTrue'     => true,
            'BooleanNull'     => false,
            'BooleanNotGiven' => false,
        ], $request->all());
    }

    public function testRequestWithBooleanNestedValues()
    {
        $userData = [
            'user' => [
                'name'          => $this->faker->name,
                'email'         => $this->faker->email,
                'password'      => Hash::make($this->faker->password),
                'activatedTrue' => true,
            ],
        ];
        $request = BooleanNestedValuesSafetyCheckedRequest::create('test', 'GET', $userData);
        $request->sanitizeRequest();
        $userData['user'] = array_add($userData['user'], 'activatedNotGiven', false);
        $this->assertEquals($userData, $request->all());
    }

    public function testRequestWithNullEntriesExclusion()
    {
        $data = [
            'notNullEntry'   => 'notNull',
            'nullEntry'      => null,
            'otherNullEntry' => null,
        ];
        $request = NullEntriesExclusionRequest::create('test', 'GET', $data);
        $request->sanitizeRequest();
        $this->assertEquals([
            'notNullEntry' => 'notNull',
        ], $request->all());
    }

    public function testRequestWithDisabledNullEntriesExclusion()
    {
        $data = [
            'notNullEntry'   => 'notNull',
            'nullEntry'      => null,
            'otherNullEntry' => null,
        ];
        $request = DisabledNullEntriesExclusionRequest::create('test', 'GET', $data);
        $request->sanitizeRequest();
        $this->assertEquals($data, $request->all());
    }

    public function testRequestWithExceptedNullEntryFromExclusion()
    {
        $data = [
            'notNullEntry'   => 'notNull',
            'nullEntry'      => null,
            'otherNullEntry' => null,
        ];
        $request = ExceptNullEntryFromNullExclusionRequest::create('test', 'GET', $data);
        $request->sanitizeRequest();
        $this->assertEquals([
            'notNullEntry'   => 'notNull',
            'otherNullEntry' => null,
        ], $request->all());
    }

    public function testRequestWithDisabledEntriesSanitizing()
    {
        $data = [
            'numberBeginningWithZero'         => '0123456',
            'numberBeginningWithZeroExcepted' => '0123456',
        ];
        $request = DisabledEntriesSanitizingRequest::create('test', 'GET', $data);
        $request->sanitizeRequest();
        $this->assertEquals($data, $request->all());
    }
}
