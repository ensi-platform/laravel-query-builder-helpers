<?php

use Ensi\QueryBuilderHelpers\Filters\ExtraFilter;
use Ensi\QueryBuilderHelpers\Tests\Models\ParentModel;

use function Pest\Laravel\postJson;

test('one value as array', function (array $filter) {
    ParentModel::factory()->createOne(['int_value' => 500, 'float_value' => null, 'string_value' => 'foo bar baz']);
    ParentModel::factory()->createOne(['int_value' => 20]);

    attachQueryBuilder('test', ParentModel::class, [
        ExtraFilter::empty('float__empty', 'float_value'),
        ExtraFilter::greaterOrEqual('int__gte', 'int_value'),
        ExtraFilter::contain('string__like', 'string_value'),
    ]);

    postJson('/test', ['filter' => $filter])
        ->assertOk()
        ->assertJsonCount(1, 'data');
})->with([
    'like' => [['string__like' => [' bar ']]],
    'range' => [['int__gte' => [100]]],
    'empty' => [['float__empty' => [true]]],
]);

test('ignore blank values', function (array $filter) {
    $expectedCount = 2;
    ParentModel::factory()->count($expectedCount)->create();

    attachQueryBuilder('test', ParentModel::class, [
        ExtraFilter::contain('string__like', 'string_value'),
    ]);

    postJson('/test', ['filter' => $filter])
        ->assertOk()
        ->assertJsonCount(2, 'data');
})->with([
    'single blank' => [['string__like' => ' ']],
    'single null' => [['string__like' => null]],
    'null in array' => [['string__like' => [null]]],
]);
