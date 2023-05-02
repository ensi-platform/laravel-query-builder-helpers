<?php

use Ensi\QueryBuilderHelpers\Filters\ExtraFilter;
use Ensi\QueryBuilderHelpers\Tests\Models\ParentModel;
use function Pest\Laravel\postJson;

test('filter exclude date success', function (string|array $value, int $count) {
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
    'date' => ['2022-03-20 00:00:00', 3],
    'start of day' => ['2022-03-17 00:00:00', 2],
    'datetime' => ['2022-03-18 12:45:15', 4],
    'multiple dates' => [['2022-03-17 12:45:15', '2022-03-20 12:45:15'], 1],
]);
