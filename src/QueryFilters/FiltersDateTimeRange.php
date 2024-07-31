<?php

namespace Ensi\QueryBuilderHelpers\QueryFilters;

use Carbon\CarbonImmutable;
use Ensi\QueryBuilderHelpers\Enums\RangeOperator;
use Ensi\QueryBuilderHelpers\Utils\Date;
use Spatie\QueryBuilder\Exceptions\InvalidFilterValue;

class FiltersDateTimeRange extends FiltersRange
{
    protected function castValue(mixed $source): CarbonImmutable
    {
        /** @var CarbonImmutable $value */
        $value = Date::makeImmutable($source) ?? throw InvalidFilterValue::make($source);

        // > (дата исключается) - сравнение с концом секунды
        // >= (дата включается) - сравнение с началом секунды
        // < (дата исключается) - сравнение с началом секунды
        // <= (дата включается) - сравнение с концом секунды
        return $this->operator === RangeOperator::GREATER || $this->operator === RangeOperator::LESS_OR_EQUAL
            ? $value->endOfSecond()
            : $value->startOfSecond();
    }
}
