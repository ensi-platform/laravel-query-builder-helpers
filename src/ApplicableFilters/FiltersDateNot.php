<?php

namespace Ensi\QueryBuilderHelpers\ApplicableFilters;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Date;

class FiltersDateNot extends FiltersBase
{
    protected function applyOne(Builder $query, mixed $value, string $column): void
    {
        $this->whereNotWithinDate($query, $value, $column);
    }

    protected function applyMulti(Builder $query, array $values, string $column): void
    {
        foreach ($values as $value) {
            $this->whereNotWithinDate($query, $value, $column);
        }
    }

    protected function castValue(mixed $source): CarbonInterface
    {
        return Date::make($source)->toImmutable();
    }

    protected function whereNotWithinDate(Builder $query, CarbonInterface $value, string $column): void
    {
        $query->whereNot(function (Builder $query) use ($value, $column) {
            $query->whereDate($column, $value)->whereNotNull($column);
        });
    }
}
