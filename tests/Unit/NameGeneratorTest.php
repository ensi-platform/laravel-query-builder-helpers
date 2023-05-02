<?php

use Ensi\QueryBuilderHelpers\Utils\NameGenerator;

test('generate missing type', function () {
    $generator = new NameGenerator([]);

    expect($generator->generate('foo', 'bar'))->toBe('foo_bar');
});

test('generate with registered suffix', function () {
    $generator = new NameGenerator(['foo' => '-bar']);

    expect($generator->generate('baz', 'foo'))->toBe('baz-bar');
});

test('register from config', function () {
    config()->set('query-builder-helpers.suffixes', ['foo' => '_bar']);
    $generator = new NameGenerator();

    expect($generator->generate('baz', 'foo'))->toBe('baz_bar');
});

test('load default config', function () {
    $generator = new NameGenerator();

    expect($generator->generate('foo', 'less'))->toBe('foo_lt');
});
