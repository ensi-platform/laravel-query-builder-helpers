<?php

use Ensi\QueryBuilderHelpers\Filters\ExtraFilter;
use Ensi\QueryBuilderHelpers\Tests\Models\ParentModel;

use function Pest\Laravel\postJson;

test('not', function (array $filter, int $count) {
    ParentModel::factory()->createMany([
        ['string_value' => 'test1', 'int_value' => 1],
        ['string_value' => 'test2', 'int_value' => 2],
        ['string_value' => 'test3', 'int_value' => 3],
        ['string_value' => null, 'int_value' => null],
    ]);

    attachQueryBuilder('test', ParentModel::class, [
        ExtraFilter::not('string__not', 'string_value'),
        ExtraFilter::not('int__not', 'int_value'),
    ]);

    postJson('/test', ['filter' => $filter])
        ->assertOk()
        ->assertJsonCount($count, 'data');
})->with([
    'string' => [['string__not' => 'test1'], 3],
    'int' => [['int__not' => 1], 3],
    'string with null value' => [['string__not' => null], 4],
    'int with null value' => [['int__not' => null], 4],
    'multiply string' => [['string__not' => ['test1', 'test2', null]], 2],
    'multiply int' => [['int__not' => [1, 2, null]], 2],
]);
