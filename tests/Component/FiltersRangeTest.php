<?php

use Ensi\QueryBuilderHelpers\Filters\ExtraFilter;
use Ensi\QueryBuilderHelpers\Tests\Models\ChildModel;
use Ensi\QueryBuilderHelpers\Tests\Models\ParentModel;
use Illuminate\Support\Facades\Date;

use function Pest\Laravel\postJson;

test('range', function (array $filter) {
    ParentModel::factory()->createOne(['int_value' => 12, 'float_value' => 129]);
    $expected = ParentModel::factory()->createOne(['int_value' => 15, 'float_value' => 125.5]);

    attachQueryBuilder('test', ParentModel::class, [
        ExtraFilter::greater('int__gt', 'int_value'),
        ExtraFilter::greaterOrEqual('int__gte', 'int_value'),
        ExtraFilter::less('float__lt', 'float_value'),
        ExtraFilter::lessOrEqual('float__lte', 'float_value'),
    ]);

    postJson('/test', ['filter' => $filter])
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $expected->id);
})->with([
    'greater' => [['int__gt' => 12]],
    'greater or equal' => [['int__gte' => 15]],
    'less' => [['float__lt' => 129]],
    'less or equal' => [['float__lte' => 125.5]],
    'multiple filters' => [['float__lte' => 125.5, 'int__gt' => 12]],
    'multiple values' => [['int__gt' => [12, 17]]],
]);

test('range relation', function (array $filter) {
    ChildModel::factory()->createOne(['int_value' => 12, 'float_value' => 129]);
    $expected = ChildModel::factory()->createOne(['int_value' => 15, 'float_value' => 125.5])->parent_id;

    attachQueryBuilder('test', ParentModel::class, [
        ExtraFilter::greater('int__gt', 'children.int_value'),
        ExtraFilter::greaterOrEqual('int__gte', 'children.int_value'),
        ExtraFilter::less('float__lt', 'children.float_value'),
        ExtraFilter::lessOrEqual('float__lte', 'children.float_value'),
    ]);

    postJson('/test', ['filter' => $filter])
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $expected);
})->with([
    'greater' => [['int__gt' => 12]],
    'greater or equal' => [['int__gte' => 15]],
    'less' => [['float__lt' => 129]],
    'less or equal' => [['float__lte' => 125.5]],
    'multiple filters' => [['float__lte' => 125.5, 'int__gt' => 12]],
]);

test('(datetime as date) range', function (array $filter, bool $timestampMs = true) {
    config()->set('query-builder-helpers.timestamp_ms', $timestampMs);

    ParentModel::factory()->createOne(['datetime_value' => Date::make('2022-08-01 15:44:11')]);
    ParentModel::factory()->createOne(['datetime_value' => Date::make('2022-08-03 00:28:36')]);
    $expected = ParentModel::factory()->createOne(['datetime_value' => Date::make('2022-08-02 11:55:08')]);

    attachQueryBuilder('test', ParentModel::class, [
        ExtraFilter::dateGreater('date__gt', 'datetime_value'),
        ExtraFilter::dateGreaterOrEqual('date__gte', 'datetime_value'),
        ExtraFilter::dateLess('date__lt', 'datetime_value'),
        ExtraFilter::dateLessOrEqual('date__lte', 'datetime_value'),
    ]);

    postJson('/test', ['filter' => $filter])
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $expected->id);
})->with([
    'exclude boundaries' => [['date__gt' => '2022-08-01T00:00:00.000000Z', 'date__lt' => '2022-08-03T00:00:00.000000Z']],
    'include start' => [['date__gte' => '2022-08-02T18:00:00.000000Z', 'date__lt' => '2022-08-03T00:00:00.000000Z']],
    'include end' => [['date__gt' => '2022-08-01T00:00:00.000000Z', 'date__lte' => '2022-08-02T07:00:00.000000Z']],
    'timestamp exclude boundaries' => [['date__gt' => 1659312000, 'date__lt' => 1659484800], false],
    'timestamp include start' => [['date__gte' => 1659463200, 'date__lt' => 1659484800], false],
    'timestamp include end' => [['date__gt' => 1659312000, 'date__lte' => 1659423600], false],
    'timestampMs exclude boundaries' => [['date__gt' => 1659312000000, 'date__lt' => 1659484800000]],
    'timestampMs include start' => [['date__gte' => 1659463200000, 'date__lt' => 1659484800000]],
    'timestampMs include end' => [['date__gt' => 1659312000000, 'date__lte' => 1659423600000]],
]);

test('(datetime as datetime) range', function (array $filter, bool $timestampMs = true) {
    config()->set('query-builder-helpers.timestamp_ms', $timestampMs);

    ParentModel::factory()->createOne(['datetime_value' => Date::make('2022-08-01 15:00:01')]);
    $expected = ParentModel::factory()->createOne(['datetime_value' => Date::make('2022-08-01 15:00:02')]);
    ParentModel::factory()->createOne(['datetime_value' => Date::make('2022-08-01 15:00:03')]);

    attachQueryBuilder('test', ParentModel::class, [
        ExtraFilter::dateTimeGreater('date__gt', 'datetime_value'),
        ExtraFilter::dateTimeGreaterOrEqual('date__gte', 'datetime_value'),
        ExtraFilter::dateTimeLess('date__lt', 'datetime_value'),
        ExtraFilter::dateTimeLessOrEqual('date__lte', 'datetime_value'),
    ]);

    postJson('/test', ['filter' => $filter])
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $expected->id);
})->with([
    'exclude boundaries' => [['date__gt' => '2022-08-01T15:00:01.000000Z', 'date__lt' => '2022-08-01T15:00:03.000000Z']],
    'include start' => [['date__gte' => '2022-08-01T15:00:02.000000Z', 'date__lt' => '2022-08-01T15:00:03.000000Z']],
    'include end' => [['date__gt' => '2022-08-01T15:00:01.000000Z', 'date__lte' => '2022-08-01T15:00:02.000000Z']],
    'timestamp exclude boundaries' => [['date__gt' => 1659366001, 'date__lt' => 1659366003], false],
    'timestamp include start' => [['date__gte' => 1659366002, 'date__lt' => 1659366003], false],
    'timestamp include end' => [['date__gt' => 1659366001, 'date__lte' => 1659366002], false],
    'timestampMs exclude boundaries' => [['date__gt' => 1659366001000, 'date__lt' => 1659366003000]],
    'timestampMs include start' => [['date__gte' => 1659366002000, 'date__lt' => 1659366003000]],
    'timestampMs include end' => [['date__gt' => 1659366001000, 'date__lte' => 1659366002000]],
]);

test('(datetime as date) range relation', function (array $filter, bool $timestampMs = true) {
    config()->set('query-builder-helpers.timestamp_ms', $timestampMs);

    ChildModel::factory()->createOne(['datetime_value' => Date::make('2022-08-01 15:44:11')]);
    ChildModel::factory()->createOne(['datetime_value' => Date::make('2022-08-03 00:28:36')]);
    $expected = ChildModel::factory()->createOne(['datetime_value' => Date::make('2022-08-02 11:55:08')])->parent_id;

    attachQueryBuilder('test', ParentModel::class, [
        ExtraFilter::dateGreater('date__gt', 'children.datetime_value'),
        ExtraFilter::dateGreaterOrEqual('date__gte', 'children.datetime_value'),
        ExtraFilter::dateLess('date__lt', 'children.datetime_value'),
        ExtraFilter::dateLessOrEqual('date__lte', 'children.datetime_value'),
    ]);

    postJson('/test', ['filter' => $filter])
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $expected);
})->with([
    'exclude boundaries' => [['date__gt' => '2022-08-01T00:00:00.000000Z', 'date__lt' => '2022-08-03T00:00:00.000000Z']],
    'include start' => [['date__gte' => '2022-08-02T18:00:00.000000Z', 'date__lt' => '2022-08-03T00:00:00.000000Z']],
    'include end' => [['date__gt' => '2022-08-01T00:00:00.000000Z', 'date__lte' => '2022-08-02T07:00:00.000000Z']],
    'timestamp exclude boundaries' => [['date__gt' => 1659312000, 'date__lt' => 1659484800], false],
    'timestamp include start' => [['date__gte' => 1659463200, 'date__lt' => 1659484800], false],
    'timestamp include end' => [['date__gt' => 1659312000, 'date__lte' => 1659423600], false],
    'timestampMs exclude boundaries' => [['date__gt' => 1659312000000, 'date__lt' => 1659484800000]],
    'timestampMs include start' => [['date__gte' => 1659463200000, 'date__lt' => 1659484800000]],
    'timestampMs include end' => [['date__gt' => 1659312000000, 'date__lte' => 1659423600000]],
]);

test('(datetime as datetime) range relation', function (array $filter, bool $timestampMs = true) {
    config()->set('query-builder-helpers.timestamp_ms', $timestampMs);

    ChildModel::factory()->createOne(['datetime_value' => Date::make('2022-08-01 15:00:01')]);
    $expected = ChildModel::factory()->createOne(['datetime_value' => Date::make('2022-08-01 15:00:02')])->parent_id;
    ChildModel::factory()->createOne(['datetime_value' => Date::make('2022-08-01 15:00:03')]);

    attachQueryBuilder('test', ParentModel::class, [
        ExtraFilter::dateTimeGreater('date__gt', 'children.datetime_value'),
        ExtraFilter::dateTimeGreaterOrEqual('date__gte', 'children.datetime_value'),
        ExtraFilter::dateTimeLess('date__lt', 'children.datetime_value'),
        ExtraFilter::dateTimeLessOrEqual('date__lte', 'children.datetime_value'),
    ]);

    postJson('/test', ['filter' => $filter])
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $expected);
})->with([
    'exclude boundaries' => [['date__gt' => '2022-08-01T15:00:01.000000Z', 'date__lt' => '2022-08-01T15:00:03.000000Z']],
    'include start' => [['date__gte' => '2022-08-01T15:00:02.000000Z', 'date__lt' => '2022-08-01T15:00:03.000000Z']],
    'include end' => [['date__gt' => '2022-08-01T15:00:01.000000Z', 'date__lte' => '2022-08-01T15:00:02.000000Z']],
    'timestamp exclude boundaries' => [['date__gt' => 1659366001, 'date__lt' => 1659366003], false],
    'timestamp include start' => [['date__gte' => 1659366002, 'date__lt' => 1659366003], false],
    'timestamp include end' => [['date__gt' => 1659366001, 'date__lte' => 1659366002], false],
    'timestampMs exclude boundaries' => [['date__gt' => 1659366001000, 'date__lt' => 1659366003000]],
    'timestampMs include start' => [['date__gte' => 1659366002000, 'date__lt' => 1659366003000]],
    'timestampMs include end' => [['date__gt' => 1659366001000, 'date__lte' => 1659366002000]],
]);
