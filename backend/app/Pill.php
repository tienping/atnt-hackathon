<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string weight_value
 * @property int qty
 */
class Pill extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pill';
}
