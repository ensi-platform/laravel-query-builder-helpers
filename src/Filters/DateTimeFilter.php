<?php

namespace Ensi\QueryBuilderHelpers\Filters;

class DateTimeFilter extends Filter
{
    public function exact(): static
    {
        return $this->addFilter(ExtraFilter::dateTimeExact(...$this->makeFilterParams(self::EQUAL)));
    }

    public function empty(): static
    {
        return $this->addFilter(ExtraFilter::empty(...$this->makeFilterParams(self::EMPTY)));
    }

    public function not(): static
    {
        return $this->addFilter(ExtraFilter::dateTimeNot(...$this->makeFilterParams(self::NOT)));
    }

    public function gt(): static
    {
        return $this->addFilter(ExtraFilter::dateTimeGreater(...$this->makeFilterParams(self::GREATER)));
    }

    public function gte(): static
    {
        return $this->addFilter(ExtraFilter::dateTimeGreaterOrEqual(...$this->makeFilterParams(self::GREATER_OR_EQUAL)));
    }

    public function lt(): static
    {
        return $this->addFilter(ExtraFilter::dateTimeLess(...$this->makeFilterParams(self::LESS)));
    }

    public function lte(): static
    {
        return $this->addFilter(ExtraFilter::dateTimeLessOrEqual(...$this->makeFilterParams(self::LESS_OR_EQUAL)));
    }
}
