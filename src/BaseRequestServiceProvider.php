<?php

namespace Okipa\LaravelBaseRequest;

use AcidSolutions\InputSanitizer\Laravel\Facades\InputSanitizer;
use AcidSolutions\InputSanitizer\Laravel\InputSanitizerServiceProvider;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class LaravelBaseRequestServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // we publish the config on demand
        $this->publishes([
            __DIR__ . '/../config/base-request.php' => config_path('base-request.php'),
        ], 'laravel-base-request');
    }

    /**
     * Register any application services.
     * This service provider is a great spot to register your various container
     * bindings with the application. As you can see, we are registering our
     * "Registrar" implementation here. You can add your own bindings too!
     *
     * @return void
     */
    public function register()
    {
        // we merge the custom configurations to the default ones
        $this->mergeConfigFrom(__DIR__ . '/../config/base-request.php', 'base-request');
        // we instantiate the package
        $this->app->singleton('Okipa\LaravelBaseRequest', function(Application $app) {
            return $app->make(LaravelBaseRequest::class);
        });
        // we load the input sanitizer package
        // https://github.com/ACID-Solutions/input-sanitizer
        $this->app->register(InputSanitizerServiceProvider::class);
        $this->app->alias('InputSanitizer', InputSanitizer::class);
    }
}
