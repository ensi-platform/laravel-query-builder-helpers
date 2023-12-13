<?php

namespace Ensi\QueryBuilderHelpers\QueryFilters;

use Illuminate\Database\Eloquent\Builder;

class FiltersNot extends FiltersBase
{
    protected function applyOne(Builder $query, mixed $value, string $column): void
    {
        $this->whereNot($query, $value, $column);
    }

    protected function applyMulti(Builder $query, array $values, string $column): void
    {
        foreach ($values as $value) {
            $this->whereNot($query, $value, $column);
        }
    }

    protected function whereNot(Builder $query, mixed $value, string $column): void
    {
        $query->whereNot(function (Builder $query) use ($value, $column) {
            $query->where($column, $value)->whereNotNull($column);
        });
    }
}
