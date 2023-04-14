<?php

namespace Ensi\QueryBuilderHelpers\Tests\Models\Factories;

use Ensi\QueryBuilderHelpers\Tests\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @template T of BaseModel
 * @extends Factory<T>
 */
abstract class BaseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'int_value' => $this->faker->numberBetween(100, 1000),
            'float_value' => $this->faker->randomFloat(2, 100, 1000),
            'bool_value' => $this->faker->boolean,
            'string_value' => $this->faker->sentence,
            'datetime_value' => $this->faker->dateTimeBetween('-30 days'),
            'date_value' => $this->faker->date,
        ];
    }
}
