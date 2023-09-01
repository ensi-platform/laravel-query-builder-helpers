<?php

namespace Ensi\QueryBuilderHelpers\Tests\Models\Factories;

use Ensi\QueryBuilderHelpers\Tests\Models\ChildModel;
use Ensi\QueryBuilderHelpers\Tests\Models\GrandChildModel;
use Ensi\QueryBuilderHelpers\Tests\Models\ParentModel;

/**
 * @extends BaseFactory<GrandChildModel>
 *
 * @method GrandChildModel createOne(array $fields = [])
 */
class GrandChildModelFactory extends BaseFactory
{
    protected $model = GrandChildModel::class;

    public function definition(): array
    {
        return array_merge(parent::definition(), [
            'parent_id' => ChildModelFactory::new(),
        ]);
    }

    public function forParent(ChildModel $parent): static
    {
        return $this->state(['parent_id' => $parent->id]);
    }
}
