<?php

namespace Ensi\QueryBuilderHelpers\QueryFilters;

use Illuminate\Database\Eloquent\Builder;

class FiltersNot extends FiltersBase
{
    protected function applyOne(Builder $query, mixed $value, string $column): void
    {
        $query->whereNotIn($column, [$value]);
    }

    protected function applyMulti(Builder $query, array $values, string $column): void
    {
        $query->whereNotIn($column, $values);
    }
}
