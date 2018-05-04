<?php

namespace Okipa\LaravelRequestSanitizer\Test\Unit;

use Illuminate\Support\Facades\Hash;
use Okipa\LaravelRequestSanitizer\Test\Requests\BooleanNestedValuesSafetyCheckedRequest;
use Okipa\LaravelRequestSanitizer\Test\Requests\BooleanValuesSafetyCheckedRequest;
use Okipa\LaravelRequestSanitizer\Test\Requests\DisabledEntriesSanitizingRequest;
use Okipa\LaravelRequestSanitizer\Test\Requests\DisabledNullEntriesExclusionRequest;
use Okipa\LaravelRequestSanitizer\Test\Requests\ExceptNullEntryFromNullExclusionRequest;
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
        $this->assertEquals($userData['user']['name'], $request->input('user.name'));
        $this->assertEquals($userData['user']['email'], $request->input('user.email'));
        $this->assertEquals($userData['user']['password'], $request->input('user.password'));
        $this->assertEquals(true, $request->input('user.activatedTrue'));
        $this->assertEquals(false, $request->input('user.activatedNotGiven'));
    }

    public function testRequestWithDisabledNullEntriesExclusion()
    {
        $data = [
            'notNullEntry'   => 'notNull',
            'nullEntry'      => null,
            'otherNullEntry' => null,
        ];
        $request = DisabledNullEntriesExclusionRequest::create('test', 'GET', $data);
        $this->assertEquals($data['notNullEntry'], $request->notNullEntry);
        $this->assertEquals($data['nullEntry'], $request->nullEntry);
        $this->assertEquals($data['otherNullEntry'], $request->otherNullEntry);
    }

    public function testRequestWithExceptedNullEntryFromExclusion()
    {
        $data = [
            'notNullEntry'   => 'notNull',
            'nullEntry'      => null,
            'otherNullEntry' => null,
        ];
        $request = ExceptNullEntryFromNullExclusionRequest::create('test', 'GET', $data);
        $this->assertEquals($data['notNullEntry'], $request->notNullEntry);
        $this->assertEquals($data['nullEntry'], $request->nullEntry);
        $this->assertEquals($data['otherNullEntry'], $request->otherNullEntry);
    }

    public function testRequestWithDisabledEntriesSanitizing()
    {
        $data = [
            'numberBeginningWithZero'         => '0123456',
            'numberBeginningWithZeroExcepted' => '0123456',
        ];
        $request = DisabledEntriesSanitizingRequest::create('test', 'GET', $data);
        $this->assertEquals($data['numberBeginningWithZero'], $request->numberBeginningWithZero);
        $this->assertEquals($data['numberBeginningWithZeroExcepted'], $request->numberBeginningWithZeroExcepted);
    }
}
