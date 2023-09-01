<?php

namespace Ensi\QueryBuilderHelpers\Tests\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int|null $int_value
 * @property float|null $float_value
 * @property string|null $string_value
 * @property CarbonInterface|null $datetime_value
 * @property CarbonInterface|null $date_value
 * @property bool|null $bool_value
 * @property array|null $array_value
 */
abstract class BaseModel extends Model
{
    protected $dates = ['datetime_value', 'date_value'];

    protected $casts = [
        'int_value' => 'int',
        'float_value' => 'float',
        'bool_value' => 'bool',
        'array_value' => 'array',
    ];
}
