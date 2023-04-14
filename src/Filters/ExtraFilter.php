<?php

namespace Ensi\QueryBuilderHelpers\Filters;

use Closure;
use Ensi\QueryBuilderHelpers\ApplicableFilters\FiltersDateExact;
use Ensi\QueryBuilderHelpers\ApplicableFilters\FiltersDateNot;
use Ensi\QueryBuilderHelpers\ApplicableFilters\FiltersDateRange;
use Ensi\QueryBuilderHelpers\ApplicableFilters\FiltersEmpty;
use Ensi\QueryBuilderHelpers\ApplicableFilters\FiltersHas;
use Ensi\QueryBuilderHelpers\ApplicableFilters\FiltersLike;
use Ensi\QueryBuilderHelpers\ApplicableFilters\FiltersPredefined;
use Ensi\QueryBuilderHelpers\ApplicableFilters\FiltersRange;
use Ensi\QueryBuilderHelpers\ApplicableFilters\NestedFilter;
use Ensi\QueryBuilderHelpers\ApplicableFilters\NestedScope;
use Ensi\QueryBuilderHelpers\Enums\LikeMode;
use Ensi\QueryBuilderHelpers\Enums\RangeOperator;
use Spatie\QueryBuilder\AllowedFilter;

class ExtraFilter
{
    public static function startWith(string $name, ?string $internalName = null): AllowedFilter
    {
        return new AllowedFilter($name, new FiltersLike(LikeMode::START_WITH), $internalName);
    }

    public static function endWith(string $name, ?string $internalName = null): AllowedFilter
    {
        return new AllowedFilter($name, new FiltersLike(LikeMode::END_WITH), $internalName);
    }

    public static function contain(string $name, ?string $internalName = null): AllowedFilter
    {
        return new AllowedFilter($name, new FiltersLike(LikeMode::CONTAIN), $internalName);
    }

    public static function greater(string $name, ?string $internalName = null): AllowedFilter
    {
        return new AllowedFilter($name, new FiltersRange(RangeOperator::GREATER), $internalName);
    }

    public static function greaterOrEqual(string $name, ?string $internalName = null): AllowedFilter
    {
        return new AllowedFilter($name, new FiltersRange(RangeOperator::GREATER_OR_EQUAL), $internalName);
    }

    public static function less(string $name, ?string $internalName = null): AllowedFilter
    {
        return new AllowedFilter($name, new FiltersRange(RangeOperator::LESS), $internalName);
    }

    public static function lessOrEqual(string $name, ?string $internalName = null): AllowedFilter
    {
        return new AllowedFilter($name, new FiltersRange(RangeOperator::LESS_OR_EQUAL), $internalName);
    }

    public static function empty(string $name, ?string $internalName = null): AllowedFilter
    {
        return new AllowedFilter($name, new FiltersEmpty(), $internalName);
    }

    public static function has(string $name, ?string $internalName = null): AllowedFilter
    {
        return new AllowedFilter($name, new FiltersHas(), $internalName);
    }

    public static function dateExact(string $name, ?string $internalName = null): AllowedFilter
    {
        return new AllowedFilter($name, new FiltersDateExact(), $internalName);
    }

    public static function dateNot(string $name, ?string $internalName = null): AllowedFilter
    {
        return new AllowedFilter($name, new FiltersDateNot(), $internalName);
    }

    public static function dateGreater(string $name, ?string $internalName = null): AllowedFilter
    {
        return new AllowedFilter($name, new FiltersDateRange(RangeOperator::GREATER), $internalName);
    }

    public static function dateGreaterOrEqual(string $name, ?string $internalName = null): AllowedFilter
    {
        return new AllowedFilter($name, new FiltersDateRange(RangeOperator::GREATER_OR_EQUAL), $internalName);
    }

    public static function dateLess(string $name, ?string $internalName = null): AllowedFilter
    {
        return new AllowedFilter($name, new FiltersDateRange(RangeOperator::LESS), $internalName);
    }

    public static function dateLessOrEqual(string $name, ?string $internalName = null): AllowedFilter
    {
        return new AllowedFilter($name, new FiltersDateRange(RangeOperator::LESS_OR_EQUAL), $internalName);
    }

    /**
     * Регистрирует набор вложенных фильтров.
     *
     * @param  string|Closure  $relation имя связи или функция, принимающая два аргумента:
     *   Builder $query - экземпляр исходного запроса
     *   fn(Builder $builder): void - функция обратного вызова, применяющая фильтры к переданному запросу
     *
     * ExtraFilter::nested(function (Builder $query, Closure $callback) {
     *      $query->whereHas('children', function (Builder $q) use ($callback) {
     *          $callback($q);
     *      });
     * }, [
     *      ExtraFilter::greater('children_value'),
     * ]);
     * @param  array|Filter[] $filters
     * @return array|AllowedFilter[]
     */
    public static function nested(string|Closure $relation, array $filters): array
    {
        $scope = new NestedScope($relation);

        return array_map(
            fn (AllowedFilter $filter) => new NestedFilter($scope, $filter),
            $filters
        );
    }

    /**
     * Создает предопределенный фильтр.
     * Фильтр применяется только если в запросе для него передано true, либо в запросе ничего не передано,
     * но фильтр определен с default(true).
     *
     * ExtraFilter::predefined(
     *      'only_future',
     *      fn (Builder $query) => $query->where('datetime_value', '>', now()->endOfDay())
     * )
     *
     * @param  string  $name Имя фильтра в http запросе
     * @param  Closure  $callback Функция с единственным параметром ссылкой на Builder.
     * @return AllowedFilter
     */
    public static function predefined(string $name, Closure $callback): AllowedFilter
    {
        return new AllowedFilter($name, new FiltersPredefined($callback));
    }
}
