<?php

use Ensi\QueryBuilderHelpers\Filters\DateFilter;
use Ensi\QueryBuilderHelpers\Filters\Filter;
use Ensi\QueryBuilderHelpers\Filters\StringFilter;
use Ensi\QueryBuilderHelpers\Filters\NumericFilter;
use Spatie\QueryBuilder\AllowedFilter;

test('it builds filters array', function (Filter $filters, array $appliedFilterNames) {
    /** @var AllowedFilter $filter */
    foreach ($filters as $filter) {
        expect($appliedFilterNames)->toContain($filter->getName());
    }
})->with([
    [
        fn () => StringFilter::make('test')->exact()->empty()->startWith()->endWith(),
        ['test', 'test_empty', 'test_llike', 'test_rlike'],
    ],
    [
        fn () => DateFilter::make('test')->exact()->lte()->gte(),
        ['test', 'test_lte', 'test_gte'],
    ],
    [
        fn () => NumericFilter::make('test')->gt()->lt(),
        ['test_gt', 'test_lt'],
    ],
]);
