<?php

namespace Ensi\QueryBuilderHelpers\Tests\Models\Factories;

use Ensi\QueryBuilderHelpers\Tests\Models\ChildModel;
use Ensi\QueryBuilderHelpers\Tests\Models\GrandChildModel;
use Ensi\QueryBuilderHelpers\Tests\Models\ParentModel;

/**
 * @extends BaseFactory<ChildModel>
 *
 * @method ChildModel createOne(array $fields = [])
 */
class ChildModelFactory extends BaseFactory
{
    protected $model = ChildModel::class;

    public function definition(): array
    {
        return array_merge(parent::definition(), [
            'parent_id' => ParentModelFactory::new(),
        ]);
    }

    public function forParent(ParentModel $parent): static
    {
        return $this->state(['parent_id' => $parent->id]);
    }
}
