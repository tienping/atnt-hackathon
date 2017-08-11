<?php
/**
 * @author Hermo Developer <dev@hermo.my>
 * @link http://www.hermo.my
 * @copyright Copyright (c) Hermo Creative Sdn. Bhd.
 */

namespace App\Http\Controllers;

use App\Pill;
use App\Timetable;
use App\Transaction;
use Illuminate\Support\Facades\Input;

/**
 * Class TriggerController
 * @package App\Http\Controllers
 */
class TriggerController extends Controller
{
    /**
     * Save trigger action
     */
    public function save()
    {
        /** @var Pill $pill */
        //TODO: change name to id
        $pill = Pill::where('name', 'Chlorothiazide')->first();
        $model = new Transaction();
        $model->timetable_id = 1;
        $model->pill_id = 1;
        $model->last_consumed_datetime = date("Y-m-d H:i:s");
        $model->last_weight_value = Input::get('last_weight_value');
        $model->last_consumed_qty = $model->last_weight_value / $pill->weight_value;
        $model->save();

        //TODO: Hide the message first

//        $controller = new MobileController();
//        $controller->send(MobileController::MESSAGE_TAKEN);

        //TODO: if got time, notify when overdose
        $notifiQty = $pill->qty * 0.2;
        if ($notifiQty >= $model->last_consumed_qty) {
            echo 'restock';
//            $controller->send(MobileController::MESSAGE_RESTOCK);
        }
        if ($model->last_consumed_qty == 0) {
            echo 'oos';
//            $controller->send(MobileController::MESSAGE_OOS);
        }
    }

    /**
     * @param string $now
     */
    public function check($now)
    {
        /** @var Timetable $timetable */
        //TODO: change pill_id to id
        $timetable = Timetable::where('pill_id', 1)->first();
        $timetableTimestamp = strtotime(date('Y-m-d '. $timetable->consume_time));
        $nowTimestamp = strtotime($now);
        $different = $nowTimestamp - $timetableTimestamp;

        if ($nowTimestamp >= $timetableTimestamp) {
            if ($different >= 5 && $different <= 8) {
                echo 'soft alert';
            } elseif ($different >= 15 && $different <= 20) {
                echo 'hard alert';
                //TODO: only have 5 credits dont waste!!!
//                $controller = new MobileController();
//                $controller->send(MobileController::MESSAGE_REMINDER);
            }
        }
    }
}