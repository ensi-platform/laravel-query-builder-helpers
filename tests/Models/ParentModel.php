<?php

namespace Ensi\QueryBuilderHelpers\Tests\Models;

use Ensi\QueryBuilderHelpers\Tests\Models\Factories\ParentModelFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ParentModel extends BaseModel
{
    protected $table = 'parents';

    public function children(): HasMany
    {
        return $this->hasMany(ChildModel::class, 'parent_id');
    }

    public static function factory(): ParentModelFactory
    {
        return ParentModelFactory::new();
    }
}
