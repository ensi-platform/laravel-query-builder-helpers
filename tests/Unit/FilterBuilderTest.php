<?php

use Ensi\QueryBuilderHelpers\Filters\DateFilter;
use Ensi\QueryBuilderHelpers\Filters\Filter;
use Ensi\QueryBuilderHelpers\Filters\InputFilter;
use Ensi\QueryBuilderHelpers\Filters\RangeFilter;
use Spatie\QueryBuilder\AllowedFilter;

test('it builds filters array', function (Filter $filters, array $appliedFilterNames) {
    /** @var AllowedFilter $filter */
    foreach ($filters as $filter) {
        expect($appliedFilterNames)->toContain($filter->getName());
    }
})->with([
    [
        fn () => InputFilter::make('test')->exact()->empty()->startWith()->endWith(),
        ['test', 'test__empty', 'test__llike', 'test__rlike'],
    ],
    [
        fn () => DateFilter::make('test')->exact()->lte()->gte(),
        ['test', 'test__lte', 'test__gte'],
    ],
    [
        fn () => RangeFilter::make('test')->gt()->lt(),
        ['test__gt', 'test__lt'],
    ],
]);
