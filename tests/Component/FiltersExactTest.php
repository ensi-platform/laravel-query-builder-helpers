<?php

use Ensi\QueryBuilderHelpers\Filters\ExtraFilter;
use Ensi\QueryBuilderHelpers\Tests\Models\ChildModel;
use Ensi\QueryBuilderHelpers\Tests\Models\ParentModel;
use Illuminate\Support\Facades\DB;

use function Pest\Laravel\postJson;

test('filter (datetime as date) single value success', function ($value, bool $timestampMs = true) {
    config()->set('query-builder-helpers.timestamp_ms', $timestampMs);

    $expected = ParentModel::factory()->createOne(['datetime_value' => '2022-03-19 12:45:14']);
    ParentModel::factory()->createMany([
        ['datetime_value' => '2022-03-20 00:00:00'],
        ['datetime_value' => '2022-03-17 00:00:00'],
        ['datetime_value' => '2022-03-17 19:59:59'],
        ['datetime_value' => null],
    ]);

    attachQueryBuilder('test', ParentModel::class, [ExtraFilter::dateExact('datetime_value')]);

    postJson('/test', ['filter' => ['datetime_value' => $value]])
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $expected->id);
})->with([
    'date' => ['2022-03-19'],
    'start of day' => ['2022-03-19 00:00:00'],
    'datetime' => ['2022-03-19 15:45:15'],
    'timestamp start of day' => [1647648000, false],
    'timestamp' => [1647704715, false],
    'timestampMs start of day' => [1647648000000],
    'timestampMs' => [1647704715000],
]);

test('filter (datetime as datetime) single value success', function ($value, bool $timestampMs = true) {
    config()->set('query-builder-helpers.timestamp_ms', $timestampMs);

    $expected = ParentModel::factory()->createOne(['datetime_value' => '2022-03-17 15:00:02']);
    ParentModel::factory()->createMany([
        ['datetime_value' => '2022-03-17 15:00:01'],
        ['datetime_value' => '2022-03-17 15:00:03'],
        ['datetime_value' => null],
    ]);

    attachQueryBuilder('test', ParentModel::class, [ExtraFilter::dateTimeExact('datetime_value')]);

    postJson('/test', ['filter' => ['datetime_value' => $value]])
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $expected->id);
})->with([
    'datetime' => ['2022-03-17 15:00:02'],
    'timestamp' => [1647529202, false],
    'timestampMs' => [1647529202000],
]);

test('filter (datetime as date) multi value one result success', function ($value, bool $timestampMs = true) {
    config()->set('query-builder-helpers.timestamp_ms', $timestampMs);

    $expected = ParentModel::factory()->createOne(['datetime_value' => '2022-03-18 12:45:14']);
    ParentModel::factory()->createMany([
        ['datetime_value' => '2022-03-19 00:00:00'],
        ['datetime_value' => '2022-03-17 00:00:00'],
        ['datetime_value' => '2022-03-17 23:59:59'],
    ]);

    attachQueryBuilder('test', ParentModel::class, [ExtraFilter::dateExact('datetime_value')]);

    postJson('/test', ['filter' => ['datetime_value' => $value]])
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $expected->id);
})->with([
    'date' => [['2022-03-18', '2022-03-16', '2022-03-20']],
    'timestamp' => [[1647561600, 1647388800, 1647734400], false],
    'timestampMs' => [[1647561600000, 1647388800000, 1647734400000]],
]);

test('filter (datetime as datetime) multi value one result success', function ($value, bool $timestampMs = true) {
    config()->set('query-builder-helpers.timestamp_ms', $timestampMs);

    $expected = ParentModel::factory()->createOne(['datetime_value' => '2022-03-17 15:00:02']);
    ParentModel::factory()->createMany([
        ['datetime_value' => '2022-03-17 15:00:01'],
        ['datetime_value' => '2022-03-17 15:00:03'],
        ['datetime_value' => null],
    ]);

    attachQueryBuilder('test', ParentModel::class, [ExtraFilter::dateTimeExact('datetime_value')]);

    postJson('/test', ['filter' => ['datetime_value' => $value]])
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $expected->id);
})->with([
    'date' => [['2022-03-17 15:00:02', '2022-03-17 15:00:04']],
    'timestamp' => [[1647529202, 1647529204], false],
    'timestampMs' => [[1647529202000, 1647529204000]],
]);

test('filter (datetime as date) multi value multi result success', function ($value, bool $timestampMs = true) {
    config()->set('query-builder-helpers.timestamp_ms', $timestampMs);

    ParentModel::factory()->createMany([
        ['datetime_value' => '2022-03-18 12:45:14'],
        ['datetime_value' => '2022-03-19 00:00:00'],
        ['datetime_value' => '2022-03-17 00:00:00'],
        ['datetime_value' => '2022-03-17 23:59:59'],
    ]);

    attachQueryBuilder('test', ParentModel::class, [ExtraFilter::dateExact('datetime_value')]);

    postJson('/test', ['filter' => ['datetime_value' => $value]])
        ->assertOk()
        ->assertJsonCount(3, 'data')
        ->assertJsonFragment(['datetime_value' => '2022-03-17T23:59:59.000000Z'])
        ->assertJsonMissing(['datetime_value' => '2022-03-19T00:00:00.000000Z']);
})->with([
    'date' => [['2022-03-17', '2022-03-18', '2022-03-20']],
    'timestamp' => [[1647475200, 1647561600, 1647734400], false],
    'timestampMs' => [[1647475200000, 1647561600000, 1647734400000]],
]);

test('filter (datetime as datetime) multi value multi result success', function ($value, bool $timestampMs = true) {
    config()->set('query-builder-helpers.timestamp_ms', $timestampMs);

    ParentModel::factory()->createMany([
        ['datetime_value' => '2022-03-17 15:00:01'],
        ['datetime_value' => '2022-03-17 15:00:02'],
        ['datetime_value' => '2022-03-17 15:00:03'],
        ['datetime_value' => '2022-03-17 15:00:04'],
    ]);

    attachQueryBuilder('test', ParentModel::class, [ExtraFilter::dateTimeExact('datetime_value')]);

    postJson('/test', ['filter' => ['datetime_value' => $value]])
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonFragment(['datetime_value' => '2022-03-17T15:00:01.000000Z'])
        ->assertJsonFragment(['datetime_value' => '2022-03-17T15:00:04.000000Z'])
        ->assertJsonMissing(['datetime_value' => '2022-03-17T15:00:02.000000Z'])
        ->assertJsonMissing(['datetime_value' => '2022-03-17T15:00:03.000000Z']);
})->with([
    'date' => [['2022-03-17 15:00:01', '2022-03-17 15:00:04']],
    'timestamp' => [[1647529201, 1647529204], false],
    'timestampMs' => [[1647529201000, 1647529204000]],
]);

test('filter date value success', function ($value, bool $timestampMs = true) {
    config()->set('query-builder-helpers.timestamp_ms', $timestampMs);

    $expected = ParentModel::factory()->createOne(['date_value' => '2022-03-18']);
    ParentModel::factory()->createMany([
        ['date_value' => '2022-03-19'],
        ['date_value' => '2022-03-17'],
        ['date_value' => '2022-03-17'],
        ['date_value' => null],
    ]);

    attachQueryBuilder('test', ParentModel::class, [ExtraFilter::dateExact('date_value')]);

    postJson('/test', ['filter' => ['date_value' => $value]])
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $expected->id);
})->with([
    'date' => ['2022-03-18'],
    'start of day' => ['2022-03-18 00:00:00'],
    'timestamp' => [1647561600, false],
    'timestampMs' => [1647561600000],
]);

test('array like', function (array $value, mixed $filter) {
    ParentModel::factory()->count(2)->create();
    $expected = ParentModel::factory()->createOne(['array_value' => $value]);

    attachQueryBuilder('test', ParentModel::class, [
        ExtraFilter::arrayExact('array_value'),
    ]);

    postJson('/test', ['filter' => ['array_value' => $filter]])
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $expected->id);
})->with([
    [['foo', 'bar'], ['foo', 'bar']],
    [['foo', 'bar'], ['bar', 'foo']],
    [['foo'], ['foo']],
    [['foo'], 'foo'],
])->skip(fn () => DB::getDriverName() === 'sqlite', 'db driver does not support this test');

test("array like don't find", function (array $value, mixed $filter) {
    ParentModel::factory()->count(2)->create();
    $expected = ParentModel::factory()->createOne(['array_value' => $value]);

    attachQueryBuilder('test', ParentModel::class, [
        ExtraFilter::arrayExact('array_value'),
    ]);

    postJson('/test', ['filter' => ['array_value' => $filter]])
        ->assertOk()
        ->assertJsonCount(0, 'data');
})->with([
    [['foo', 'bar'], 'foo'],
    [['foo', 'bar'], ['foo']],
    [['foo', 'bar'], ['bar']],
    [['foo'], ['foo', 'bar']],
    [['foo'], ['bar', 'foo']],
])->skip(fn () => DB::getDriverName() === 'sqlite', 'db driver does not support this test');

test('array like relation', function (array $value, mixed $filter) {
    ChildModel::factory()->count(2)->create();
    $expected = ChildModel::factory()->createOne(['array_value' => $value]);

    attachQueryBuilder('test', ParentModel::class, [
        ExtraFilter::arrayContain('child__array_value', 'children.array_value'),
    ]);

    postJson('/test', ['filter' => ['child__array_value' => $filter]])
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $expected->id);
})->with([
    [['foo', 'bar'], ['foo', 'bar']],
    [['foo', 'bar'], ['bar', 'foo']],
    [['foo'], ['foo']],
    [['foo'], 'foo'],
])->skip(fn () => DB::getDriverName() === 'sqlite', 'db driver does not support this test');
