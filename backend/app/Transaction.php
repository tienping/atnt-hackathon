<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int timetable_id
 * @property int pill_id
 * @property mixed last_consumed_datetime
 * @property mixed last_weight_value
 * @property mixed last_consumed_qty
 * Class Transaction
 * @package App
 */
class Transaction extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'transaction';
}
