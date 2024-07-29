<?php

use Ensi\QueryBuilderHelpers\Filters\ExtraFilter;
use Ensi\QueryBuilderHelpers\Tests\Models\ParentModel;

use function Pest\Laravel\postJson;

test('filter exclude (datetime as datetime) success', function (string|int|array $value, int $count, bool $timestampMs = true) {
    config()->set('query-builder-helpers.timestamp_ms', $timestampMs);

    ParentModel::factory()->createMany([
        ['datetime_value' => '2022-03-17 15:00:01'],
        ['datetime_value' => '2022-03-17 15:00:02'],
        ['datetime_value' => '2022-03-17 15:00:03'],
        ['datetime_value' => null],
    ]);

    attachQueryBuilder('test', ParentModel::class, [
        ExtraFilter::dateTimeNot('datetime__not', 'datetime_value'),
    ]);

    postJson('/test', ['filter' => ['datetime__not' => $value]])
        ->assertOk()
        ->assertJsonCount($count, 'data');
})->with([
    'datetime' => ['2022-03-17 15:00:01', 3],
    'multiple datetimes' => [['2022-03-17 15:00:01', '2022-03-17 15:00:02'], 2],
    'timestamp' => [1647529201, 3, false],
    'timestampMs' => [1647529201000, 3],
    'multiple timestamps' => [[1647529201, 1647529202], 2, false],
    'multiple timestampsMs' => [[1647529201000, 1647529202000], 2],
]);
