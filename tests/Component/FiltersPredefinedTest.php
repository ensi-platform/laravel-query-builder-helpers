<?php

use Ensi\QueryBuilderHelpers\Filters\ExtraFilter;
use Ensi\QueryBuilderHelpers\Tests\Models\GrandChildModel;
use Ensi\QueryBuilderHelpers\Tests\Models\ParentModel;
use Illuminate\Contracts\Database\Eloquent\Builder;
use function Pest\Laravel\postJson;

test('enable predefined in request', function () {
    $expected = ParentModel::factory()->createOne(['datetime_value' => now()->addDay()]);
    ParentModel::factory()->createOne();

    attachQueryBuilder('test', ParentModel::class, [
        ExtraFilter::predefined('only_future', fn (Builder $query) => $query->where('datetime_value', '>', now()->endOfDay())),
    ]);

    postJson('/test', ['filter' => ['only_future' => true]])
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $expected->id);
});

test('disable predefined in request', function () {
    ParentModel::factory()->createOne(['datetime_value' => now()->addDay()]);
    ParentModel::factory()->createOne();

    attachQueryBuilder('test', ParentModel::class, [
        ExtraFilter::predefined('only_future', fn (Builder $query) => $query->where('datetime_value', '>', now()->endOfDay())),
    ]);

    postJson('/test', ['filter' => ['only_future' => false]])
        ->assertOk()
        ->assertJsonCount(2, 'data');
});

test('enable predefined by default', function () {
    $expected = ParentModel::factory()->createOne(['datetime_value' => now()->addDay()]);
    ParentModel::factory()->createOne();

    attachQueryBuilder('test', ParentModel::class, [
        ExtraFilter::predefined(
            'only_future',
            fn (Builder $query) => $query->where('datetime_value', '>', now()->endOfDay())
        )->default(true),
    ]);

    postJson('/test', ['filter' => new stdClass()])
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $expected->id);
});

test('nested predefined', function () {
    $expected = GrandChildModel::factory()->createOne(['datetime_value' => now()->addDay()])->parent;
    GrandChildModel::factory()->createOne();

    attachQueryBuilder('test', ParentModel::class, [
        ...ExtraFilter::nested('children.children', [
            ExtraFilter::predefined(
                'only_future',
                fn (Builder $query) => $query->where('datetime_value', '>', now()->endOfDay())
            )->default(true),
        ]),
    ]);

    postJson('/test', ['filter' => new stdClass()])
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $expected->parent_id);
});
