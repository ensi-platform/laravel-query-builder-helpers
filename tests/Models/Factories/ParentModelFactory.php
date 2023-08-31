<?php

namespace Ensi\QueryBuilderHelpers\Tests\Models\Factories;

use Ensi\QueryBuilderHelpers\Tests\Models\ParentModel;

/**
 * @extends BaseFactory<ParentModel>
 *
 * @method ParentModel createOne(array $fields = [])
 */
class ParentModelFactory extends BaseFactory
{
    protected $model = ParentModel::class;
}
