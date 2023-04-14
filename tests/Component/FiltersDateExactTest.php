<?php

use Ensi\QueryBuilderHelpers\Filters\ExtraFilter;
use Ensi\QueryBuilderHelpers\Tests\Models\ParentModel;
use function Pest\Laravel\postJson;

test('filter datetime single value success', function ($value) {
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
    'datetime' => ['2022-03-18 12:45:15'],
]);

test('filter datetime multi value one result success', function () {
    $expected = ParentModel::factory()->createOne(['datetime_value' => '2022-03-18 12:45:14']);
    ParentModel::factory()->createMany([
        ['datetime_value' => '2022-03-19 00:00:00'],
        ['datetime_value' => '2022-03-17 00:00:00'],
        ['datetime_value' => '2022-03-17 23:59:59'],
    ]);

    attachQueryBuilder('test', ParentModel::class, [ExtraFilter::dateExact('datetime_value')]);

    postJson('/test', ['filter' => ['datetime_value' => ['2022-03-18', '2022-03-16', '2022-03-20']]])
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $expected->id);
});

test('filter datetime multi value multi result success', function () {
    ParentModel::factory()->createMany([
        ['datetime_value' => '2022-03-18 12:45:14'],
        ['datetime_value' => '2022-03-19 00:00:00'],
        ['datetime_value' => '2022-03-17 00:00:00'],
        ['datetime_value' => '2022-03-17 23:59:59'],
    ]);

    attachQueryBuilder('test', ParentModel::class, [ExtraFilter::dateExact('datetime_value')]);

    postJson('/test', ['filter' => ['datetime_value' => ['2022-03-17', '2022-03-18', '2022-03-20']]])
        ->assertOk()
        ->assertJsonCount(3, 'data')
        ->assertJsonFragment(['datetime_value' => '2022-03-17T23:59:59.000000Z'])
        ->assertJsonMissing(['datetime_value' => '2022-03-19T00:00:00.000000Z']);
});

test('filter date value success', function ($value) {
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
]);
