<?php

use Ensi\QueryBuilderHelpers\Filters\ExtraFilter;
use Ensi\QueryBuilderHelpers\Tests\Models\ChildModel;
use Ensi\QueryBuilderHelpers\Tests\Models\ParentModel;

use function Pest\Laravel\postJson;

test('empty', function (array $filter) {
    ChildModel::factory()->count(2)->create();
    $expected = ParentModel::factory()->createOne(['string_value' => null]);
    ChildModel::factory()->forParent($expected)->createOne(['string_value' => null]);

    attachQueryBuilder('test', ParentModel::class, [
        ExtraFilter::empty('string__empty', 'string_value'),
        ExtraFilter::empty('child__empty', 'children.string_value'),
    ]);

    postJson('/test', ['filter' => $filter])
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $expected->id);
})->with([
    'self' => [['string__empty' => true]],
    'relation' => [['child__empty' => true]],
    'self with int' => [['string__empty' => 1]],
]);

test('not empty', function (array $filter) {
    $expected = ParentModel::factory()->createOne();
    ChildModel::factory()->forParent($expected)->createOne();
    ChildModel::factory()
        ->forParent(ParentModel::factory()->createOne(['string_value' => null]))
        ->createOne(['string_value' => null]);

    attachQueryBuilder('test', ParentModel::class, [
        ExtraFilter::empty('string__empty', 'string_value'),
        ExtraFilter::empty('child__empty', 'children.string_value'),
    ]);

    postJson('/test', ['filter' => $filter])
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $expected->id);
})->with([
    'self' => [['string__empty' => false]],
    'relation' => [['child__empty' => false]],
    'self with int' => [['string__empty' => 0]],
]);

test('empty with multiple values not supported', function () {
    attachQueryBuilder('test', ParentModel::class, [
        ExtraFilter::empty('string__empty', 'string_value'),
    ]);

    postJson('/test', ['filter' => ['string__empty' => [true, false]]])
        ->assertStatus(500);
});
