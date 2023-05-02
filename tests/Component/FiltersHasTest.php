<?php

use Ensi\QueryBuilderHelpers\Filters\ExtraFilter;
use Ensi\QueryBuilderHelpers\Tests\Models\GrandChildModel;
use Ensi\QueryBuilderHelpers\Tests\Models\ParentModel;
use function Pest\Laravel\postJson;

test('has', function (array $filter) {
    $grandChild = GrandChildModel::factory()->createOne();

    attachQueryBuilder('test', ParentModel::class, [
        ExtraFilter::has('child__empty', 'children'),
        ExtraFilter::has('grand_child__empty', 'children.children'),
    ]);

    postJson('/test', ['filter' => $filter])
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $grandChild->parent->parent->id);
})->with([
    'direct relation' => [['child__empty' => false]],
    'direct relation with int' => [['child__empty' => 0]],
    'nested relation' => [['grand_child__empty' => false]],
]);

test('doesnt have', function (array $filter) {
    GrandChildModel::factory()->createOne();

    attachQueryBuilder('test', ParentModel::class, [
        ExtraFilter::has('child__empty', 'children'),
        ExtraFilter::has('grand_child__empty', 'children.children'),
    ]);

    postJson('/test', ['filter' => $filter])
        ->assertOk()
        ->assertJsonCount(0, 'data');
})->with([
    'direct relation' => [['child__empty' => true]],
    'direct relation with int' => [['child__empty' => 1]],
    'nested relation' => [['grand_child__empty' => true]],
]);
