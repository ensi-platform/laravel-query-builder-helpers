<?php

use Ensi\QueryBuilderHelpers\Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Spatie\QueryBuilder\QueryBuilder;

uses(TestCase::class)->in(__DIR__);

/**
 * @param  string  $path
 * @param  class-string<\Illuminate\Database\Eloquent\Model>  $modelClass
 * @param  array  $filters
 * @return void
 */
function attachQueryBuilder(string $path, string $modelClass, array $filters): void
{
    Route::post($path, function (Request $request) use ($modelClass, $filters) {
        $builder = new QueryBuilder($modelClass::query(), $request);
        $builder->allowedFilters($filters);

        return ['data' => $builder->get()];
    });
}
