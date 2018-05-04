<?php

namespace Okipa\LaravelRequestSanitizer;

use Okipa\DataSanitizer\Laravel\Facades\DataSanitizer;
use Okipa\DataSanitizer\Laravel\DataSanitizerServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class RequestSanitizerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // we load the data sanitizer package
        // https://github.com/Okipa/laravel-request-sanitizer
        $this->app->register(DataSanitizerServiceProvider::class);
        $loader = AliasLoader::getInstance();
        $loader->alias('DataSanitizer', DataSanitizer::class);
    }
}
