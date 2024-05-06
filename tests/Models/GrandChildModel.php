<?php

namespace Ensi\QueryBuilderHelpers\Tests\Models;

use Ensi\QueryBuilderHelpers\Tests\Models\Factories\GrandChildModelFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $parent_id
 * @property-read ChildModel|null $parent
 */
class GrandChildModel extends BaseModel
{
    protected $table = 'grand_children';

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ChildModel::class, 'parent_id');
    }

    public static function factory(): GrandChildModelFactory
    {
        return GrandChildModelFactory::new();
    }
}
