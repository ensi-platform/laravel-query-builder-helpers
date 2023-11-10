<?php

namespace Ensi\QueryBuilderHelpers\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class PlugFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): void
    {
    }
}
