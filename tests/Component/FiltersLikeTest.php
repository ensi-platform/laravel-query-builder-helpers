<?php

use Ensi\QueryBuilderHelpers\Filters\ExtraFilter;
use Ensi\QueryBuilderHelpers\Tests\Models\ChildModel;
use Ensi\QueryBuilderHelpers\Tests\Models\ParentModel;
use Illuminate\Support\Facades\DB;
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

test('array like', function (array $value, mixed $filter) {
    ParentModel::factory()->count(2)->create();
    $expected = ParentModel::factory()->createOne(['array_value' => $value]);

    attachQueryBuilder('test', ParentModel::class, [
        ExtraFilter::arrayContain('array__llike', 'array_value'),
    ]);

    postJson('/test', ['filter' => ['array__llike' => $filter]])
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $expected->id);
})->with([
    [['foo', 'bar'], ['foo', 'bar']],
    [['bar', 'foo'], ['foo', 'bar']],
    [['foo', 'bar'], ['foo']],
    [['foo'], ['foo', 'bar']],
    [['foo'], ['foo']],
    [['foo'], 'foo'],
])->skip(fn () => DB::getDriverName() === 'sqlite', 'db driver does not support this test');

test("array like don't find", function (array $value, mixed $filter) {
    ParentModel::factory()->count(2)->create();
    $expected = ParentModel::factory()->createOne(['array_value' => $value]);

    attachQueryBuilder('test', ParentModel::class, [
        ExtraFilter::arrayContain('array__llike', 'array_value'),
    ]);

    postJson('/test', ['filter' => ['array__llike' => $filter]])
        ->assertOk()
        ->assertJsonCount(0, 'data');
})->with([
    [['foo', 'bar'], ['baz']],
    [['foo', 'bar'], 'baz'],
])->skip(fn () => DB::getDriverName() === 'sqlite', 'db driver does not support this test');

test('array like relation', function (array $value, mixed $filter) {
    ChildModel::factory()->count(2)->create();
    $expected = ChildModel::factory()->createOne(['array_value' => $value]);

    attachQueryBuilder('test', ParentModel::class, [
        ExtraFilter::arrayContain('child__llike', 'children.array_value'),
    ]);

    postJson('/test', ['filter' => ['child__llike' => $filter]])
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $expected->id);
})->with([
    [['foo', 'bar'], ['foo', 'bar']],
    [['bar', 'foo'], ['foo', 'bar']],
    [['foo', 'bar'], ['foo']],
    [['foo'], ['foo', 'bar']],
    [['foo'], ['foo']],
    [['foo'], 'foo'],
])->skip(fn () => DB::getDriverName() === 'sqlite', 'db driver does not support this test');
