<?php

use Ensi\QueryBuilderHelpers\Filters\ExtraFilter;
use Ensi\QueryBuilderHelpers\Tests\Models\ChildModel;
use Ensi\QueryBuilderHelpers\Tests\Models\ParentModel;
use function Pest\Laravel\postJson;

test('like', function (array $filter) {
    ParentModel::factory()->count(2)->create();
    $expected = ParentModel::factory()->createOne(['string_value' => 'foo bar baz qux']);

    attachQueryBuilder('test', ParentModel::class, [
        ExtraFilter::startWith('string__llike', 'string_value'),
        ExtraFilter::endWith('string__rlike', 'string_value'),
        ExtraFilter::contain('string__like', 'string_value'),
    ]);

    postJson('/test', ['filter' => $filter])
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $expected->id);
})->with([
    'start with' => [['string__llike' => 'foo bar']],
    'end with' => [['string__rlike' => 'baz qux']],
    'contain' => [['string__like' => 'bar baz']],
]);

test('like relation', function (array $filter) {
    ChildModel::factory()->count(2)->create();
    $expected = ChildModel::factory()->createOne(['string_value' => 'foo bar baz qux']);

    attachQueryBuilder('test', ParentModel::class, [
        ExtraFilter::startWith('child__llike', 'children.string_value'),
        ExtraFilter::endWith('child__rlike', 'children.string_value'),
        ExtraFilter::contain('child__like', 'children.string_value'),
    ]);

    postJson('/test', ['filter' => $filter])
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $expected->id);
})->with([
    'start with' => [['child__llike' => 'foo bar']],
    'end with' => [['child__rlike' => 'baz qux']],
    'contain' => [['child__like' => 'bar baz']],
]);

test('like multi value', function (array $filter) {
    ParentModel::factory()->createOne(['string_value' => 'foo bar']);
    ParentModel::factory()->createOne(['string_value' => 'baz qux']);

    attachQueryBuilder('test', ParentModel::class, [
        ExtraFilter::startWith('string__llike', 'string_value'),
        ExtraFilter::endWith('string__rlike', 'string_value'),
        ExtraFilter::contain('string__like', 'string_value'),
    ]);

    postJson('/test', ['filter' => $filter])
        ->assertOk()
        ->assertJsonCount(2, 'data');
})->with([
    'start with' => [['string__llike' => ['foo', 'baz']]],
    'end with' => [['string__rlike' => ['bar', 'qux']]],
    'contain' => [['string__like' => ['foo', 'qux']]],
]);
