<?php

namespace Ensi\QueryBuilderHelpers\Tests;

use Ensi\QueryBuilderHelpers\QueryBuilderHelpersServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\QueryBuilder\QueryBuilderServiceProvider;

class TestCase extends Orchestra
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        $this->artisan('migrate')->run();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Ensi\\QueryBuilderHelpers\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app): array
    {
        return [
            QueryBuilderServiceProvider::class,
            QueryBuilderHelpersServiceProvider::class,
        ];
    }
}
