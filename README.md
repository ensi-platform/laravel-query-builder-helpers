# laravel-query-builder-helpers

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ensi/laravel-query-builder-helpers.svg?style=flat-square)](https://packagist.org/packages/ensi/laravel-query-builder-helpers)
[![Tests](https://github.com/ensi-platform/laravel-php-rdkafka/actions/workflows/run-tests.yml/badge.svg?branch=master)](https://github.com/ensi-platform/laravel-php-rdkafka/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/ensi/laravel-query-builder-helpers.svg?style=flat-square)](https://packagist.org/packages/ensi/laravel-query-builder-helpers)

The laravel-query-builder-helper package is a set of classes that simplify the creation of available filters for the laravel-query-builder package from Spatie.

## Installation

You can install the package via composer:

```bash
composer require ensi/laravel-query-builder-helpers
```

## Version Compatibility

| Laravel query builder helpers | Laravel                    | PHP            |
|-------------------------------|----------------------------|----------------|
| ^0.1.0                        | ^9.x                       | ^8.1           |
| ^0.1.4                        | ^9.x \|\| ^10.x            | ^8.1           |
| ^0.1.7                        | ^9.x \|\| ^10.x \|\| ^11.x | ^8.1           |

## Basic Usage

### Creating a filter

Filters are created by applying the static **make** method and then calling the chain of filter methods.

```php
use Ensi\QueryBuilderHelpers\Filters\StringFilter;

StringFilter::make('name')->contain()->endWith()->empty();
```

The following filter classes are currently available:

- StringFilter
- NumericFilter
- DateFilter
- PlugFilter (a stub for passing additional parameters to another filter)
- ExtraFilter (described in the section "Additional filters")

Each filter type has its own suffix, which is added to the name passed to the **make** method.
For example, by default, the filter **empty** has the suffix **_empty**, and the filter **gt** is **_gt**:

```php
NumericFilter::make('rank')->exact()->empty()->gt()->lt();
```
As a result, we will get four filtering options available for search queries.

- rank
- rank_empty
- rank_gt
- rank_lt

### Passing filters to the allowed Filters method

To transfer the received filters to the `allowedFilters` method of the Spatie package, the array will need to be destructured.

```php
$this->allowedFilters([
    ...NumericFilter::make('rank')->exact()->empty()->gt()->lt(),
]);
```

### Additional filters

The **ExtraFilter** class is used by the aforementioned classes, but can also be used separately.

Useful methods include:

- nested (registers a set of nested filters)
- predefined (creates a predefined filter that includes complex filtering)
- and others

```php
...ExtraFilter::nested('page', [
    ...InputFilter::make('title', 'page_title')->empty()->exact()->contain(),
]),
```

## Configuration

In the file **config.php ** you can customize the applied suffixes.
You can also set up the like operator used in search queries there.

```php
'suffixes' => [
    'equal' => '',
    'greater' => '_gt',
    'less' => '_lt',
    ...
],

'like_operator' => 'LIKE',
```

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

### Testing

1. composer install
2. composer test

By default, testing takes place using in-memory DataBase SQLite.
SQLite does not support some functions, for example: json_contains.
To test these functions, copy `phpunit.xml.dist` to `phpunit.xml` and specify the configuration with connection to another database in the *php* section.
When writing such tests, use the *skip* function to skip tests using the default connection.

```php
->skip(fn () => DB::getDriverName() === 'sqlite', 'db driver does not support this test');
```

## Security Vulnerabilities

Please review [our security policy](.github/SECURITY.md) on how to report security vulnerabilities.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
