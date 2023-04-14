<?php

use Ensi\QueryBuilderHelpers\Filters\ExtraFilter;
use Ensi\QueryBuilderHelpers\Tests\Models\ChildModel;
use Ensi\QueryBuilderHelpers\Tests\Models\GrandChildModel;
use Ensi\QueryBuilderHelpers\Tests\Models\ParentModel;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as RawBuilder;
use function Pest\Laravel\postJson;
use Spatie\QueryBuilder\AllowedFilter;

test('relation by name', function () {
    ChildModel::factory()->count(2)->create(['int_value' => 10]);
    $expected = ChildModel::factory()->createOne(['int_value' => 10, 'float_value' => 50]);

    attachQueryBuilder('test', ParentModel::class, [
        ...ExtraFilter::nested('children', [
            AllowedFilter::exact('int_value'),
            ExtraFilter::lessOrEqual('float_value__lte', 'float_value'),
        ]),
    ]);

    $filter = ['int_value' => 10, 'float_value__lte' => 60.0];

    postJson('/test', ['filter' => $filter])
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $expected->parent_id);
});

test('relation with callback', function () {
    ChildModel::factory()->count(2)->create(['int_value' => 10]);
    $expected = ChildModel::factory()->createOne(['int_value' => 10, 'float_value' => 50]);

    attachQueryBuilder('test', ParentModel::class, [
        ...ExtraFilter::nested(
            fn (Builder $query, Closure $callback) => $query->whereHas('children', fn (Builder $q) => $callback($q)),
            [
                AllowedFilter::exact('int_value'),
                ExtraFilter::lessOrEqual('float_value__lte', 'float_value'),
            ]
        ),
    ]);

    $filter = ['int_value' => 10, 'float_value__lte' => 60.0];

    postJson('/test', ['filter' => $filter])
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $expected->parent_id);
});

test('relation with additional condition', function () {
    ChildModel::factory()->count(2)->create(['int_value' => 10, 'float_value' => 50]);
    $expected = ChildModel::factory()->createOne(['int_value' => 10, 'float_value' => 50, 'string_value' => 'foo']);

    $children = function (Builder $query, Closure $callback) {
        $query->whereIn('id', function (RawBuilder $raw) use ($callback) {
            $model = new ChildModel();
            $q = $model->newEloquentBuilder($raw)
                ->setModel($model)
                ->select('id')
                ->where('string_value', 'foo');

            $callback($q);
        });
    };

    attachQueryBuilder('test', ParentModel::class, [
        ...ExtraFilter::nested($children, [
            AllowedFilter::exact('int_value'),
            ExtraFilter::lessOrEqual('float_value__lte', 'float_value'),
        ]),
    ]);

    $filter = ['int_value' => 10, 'float_value__lte' => 60.0];

    postJson('/test', ['filter' => $filter])
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $expected->parent_id);
});

test('deep relation', function () {
    GrandChildModel::factory()->createOne(['string_value' => 'foo']);
    $expected = GrandChildModel::factory()->createOne(['int_value' => 40, 'string_value' => 'foo'])->parent;

    attachQueryBuilder('test', ParentModel::class, [
        ...ExtraFilter::nested('children.children', [
            AllowedFilter::exact('string_value'),
            ExtraFilter::lessOrEqual('int_value__lt', 'int_value'),
        ]),
    ]);

    $filter = ['int_value__lt' => 100, 'string_value' => 'foo'];

    postJson('/test', ['filter' => $filter])
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $expected->parent_id);
});

test('belong to', function () {
    ChildModel::factory()->createOne();
    $expected = ChildModel::factory()
        ->forParent(ParentModel::factory()->createOne(['int_value' => 45]))
        ->createOne();

    attachQueryBuilder('test', ChildModel::class, [
        ...ExtraFilter::nested('parent', [
            ExtraFilter::less('parent_int__lt', 'int_value'),
        ]),
    ]);

    postJson('/test', ['filter' => ['parent_int__lt' => 50]])
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $expected->id);
});

test('multiple nested', function () {
    GrandChildModel::factory()->count(2)->create();
    $expected = ChildModel::factory()
        ->forParent(ParentModel::factory()->createOne(['int_value' => 45]))
        ->createOne();

    GrandChildModel::factory()
        ->forParent($expected)
        ->createOne(['datetime_value' => now()->addDays(2)]);

    attachQueryBuilder('test', ChildModel::class, [
        ...ExtraFilter::nested('parent', [
            ExtraFilter::less('parent_int__lt', 'int_value'),
        ]),
        ...ExtraFilter::nested('children', [
            ExtraFilter::greater('child_date__gt', 'datetime_value'),
        ]),
    ]);

    $filter = ['parent_int__lt' => 50, 'child_date__gt' => now()->addHour()->toJSON()];

    postJson('/test', ['filter' => $filter])
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $expected->id);
});
