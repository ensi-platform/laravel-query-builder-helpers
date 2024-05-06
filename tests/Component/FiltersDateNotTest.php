<?php

use Ensi\QueryBuilderHelpers\Filters\ExtraFilter;
use Ensi\QueryBuilderHelpers\Tests\Models\ParentModel;

use function Pest\Laravel\postJson;

test('filter exclude date success', function (string|int|array $value, int $count, bool $timestampMs = true) {
    config()->set('query-builder-helpers.timestamp_ms', $timestampMs);

    ParentModel::factory()->createMany([
        ['datetime_value' => '2022-03-20 00:00:00'],
        ['datetime_value' => '2022-03-17 00:00:00'],
        ['datetime_value' => '2022-03-17 19:59:59'],
        ['datetime_value' => null],
    ]);

    attachQueryBuilder('test', ParentModel::class, [
        ExtraFilter::dateNot('datetime__not', 'datetime_value'),
    ]);

    postJson('/test', ['filter' => ['datetime__not' => $value]])
        ->assertOk()
        ->assertJsonCount($count, 'data');
})->with([
    'date' => ['2022-03-20', 3],
    'start of day' => ['2022-03-17 00:00:00', 2],
    'datetime' => ['2022-03-18 12:45:15', 4],
    'multiple dates' => [['2022-03-17 12:45:15', '2022-03-20 12:45:15'], 1],
    'timestamp start of day' => [1647475200, 2, false],
    'timestamp' => [1647607515, 4, false],
    'timestampMs start of day' => [1647475200000, 2],
    'timestampMs' => [1647607515000, 4],
    'multiple timestamps' => [[1647521115, 1647780315], 1, false],
    'multiple timestampsMs' => [[1647521115000, 1647780315000], 1],
]);
