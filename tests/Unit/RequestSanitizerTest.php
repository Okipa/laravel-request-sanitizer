<?php

namespace Okipa\LaravelRequestSanitizer\Test\Unit;

use Hash;
use Illuminate\Support\Arr;
use Okipa\LaravelRequestSanitizer\RequestSanitizer;
use Okipa\LaravelRequestSanitizer\Test\Requests\AuthorizationCheckRequest;
use Okipa\LaravelRequestSanitizer\Test\Requests\BeforeSanitizingRequest;
use Okipa\LaravelRequestSanitizer\Test\Requests\DisabledEntriesSanitizingRequest;
use Okipa\LaravelRequestSanitizer\Test\Requests\DisabledNullEntriesExclusionRequest;
use Okipa\LaravelRequestSanitizer\Test\Requests\ExceptNullEntryFromNullExclusionRequest;
use Okipa\LaravelRequestSanitizer\Test\Requests\NestedValuesSafetyCheckedRequest;
use Okipa\LaravelRequestSanitizer\Test\Requests\NullEntriesExclusionRequest;
use Okipa\LaravelRequestSanitizer\Test\Requests\NumberBeginningWithZeroNestedValuesSanitizingRequest;
use Okipa\LaravelRequestSanitizer\Test\Requests\ValuesSafetyCheckedRequest;
use Okipa\LaravelRequestSanitizer\Test\RequestSanitizerTestCase;

class RequestSanitizerTest extends RequestSanitizerTestCase
{
    public function testRequestSanitizingWithNumberBeginningWithZeroValues()
    {
        $data = [
            'numberBeginningWithZero'         => '0123456',
            'numberBeginningWithZeroExcepted' => '0123456',
        ];
        $testRequest = new class extends RequestSanitizer
        {
            protected $exceptFromSanitize = ['numberBeginningWithZeroExcepted'];
        };
        $request = $testRequest->create('test', 'GET', $data);
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

    public function testRequestWithSafetyValues()
    {
        $request = ValuesSafetyCheckedRequest::create('test', 'GET', [
            'booleanTrue' => true,
            'booleanNull' => null,
            'arrayFilled' => ['test1', 'test2'],
            'arrayEmpty'  => [],
        ]);
        $request->sanitizeRequest();
        $this->assertEquals([
            'booleanTrue'     => true,
            'booleanNull'     => false,
            'booleanNotGiven' => false,
            'arrayFilled'     => ['test1', 'test2'],
            'arrayEmpty'      => [],
            'arrayNotGiven'   => [],
        ], $request->all());
    }

    public function testRequestWithSafetyNestedValues()
    {
        $userData = [
            'user' => [
                'name'              => $this->faker->name,
                'email'             => $this->faker->email,
                'password'          => Hash::make($this->faker->password),
                'activatedTrue'     => true,
                'permissionsFilled' => ['test1', 'test2'],
            ],
        ];
        $request = NestedValuesSafetyCheckedRequest::create('test', 'GET', $userData);
        $request->sanitizeRequest();
        $userData['user'] = Arr::add($userData['user'], 'activatedNotGiven', false);
        $userData['user'] = Arr::add($userData['user'], 'permissionsNotGiven', []);
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

    public function testRequestWithSanitizedDataInAuthorization()
    {
        $data = [
            'numberBeginningWithZero'         => '0123456',
            'numberBeginningWithZeroExcepted' => '0123456',
        ];
        $request = AuthorizationCheckRequest::create('test', 'GET', $data);
        $request->sanitizeRequest();
        $request->authorize(); // the authorize method is always called after the sanitizing treatment
        $this->assertEquals([
            'numberBeginningWithZero'         => 123456,
            'numberBeginningWithZeroExcepted' => '0123456',
        ], $request->sanitizedData);
        $this->assertEquals([
            'numberBeginningWithZero'         => 123456,
            'numberBeginningWithZeroExcepted' => '0123456',
        ], $request->all());
    }
}
