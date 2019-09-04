<?php

namespace Okipa\LaravelRequestSanitizer\Test;

use Faker\Factory;
use Okipa\LaravelRequestSanitizer\RequestSanitizerServiceProvider;
use Orchestra\Testbench\TestCase;

abstract class RequestSanitizerTestCase extends TestCase
{
    protected $faker;

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->faker = Factory::create();
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [RequestSanitizerServiceProvider::class];
    }
}
