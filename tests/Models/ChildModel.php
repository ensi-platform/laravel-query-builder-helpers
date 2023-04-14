<?php

namespace Ensi\QueryBuilderHelpers\Tests\Models;

use Ensi\QueryBuilderHelpers\Tests\Models\Factories\BaseFactory;
use Ensi\QueryBuilderHelpers\Tests\Models\Factories\ChildModelFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property int|null $parent_id
 * @property-read ParentModel|null $parent
 * @property-read Collection<GrandChildModel> $children
 */
class ChildModel extends BaseModel
{
    protected $table = 'children';

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ParentModel::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(GrandChildModel::class, 'parent_id');
    }

    /**
     * @return ChildModelFactory|BaseFactory<self>
     */
    public static function factory(): ChildModelFactory
    {
        return ChildModelFactory::new();
    }
}
