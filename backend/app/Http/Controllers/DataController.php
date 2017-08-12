<?php
/**
 * @author Hermo Developer <dev@hermo.my>
 * @link http://www.hermo.my
 * @copyright Copyright (c) Hermo Creative Sdn. Bhd.
 */

namespace App\Http\Controllers;

use App\Transaction;
use Illuminate\Support\Facades\DB;

/**
 * Class DataController
 * @package App\Http\Controllers
 */
class DataController extends Controller
{
    /**
     * Know the overdose and under dose
     * @return array
     */
    public function dose()
    {
        $rows = DB::table('transaction')
            ->select(DB::raw('date(last_consumed_datetime) as day, count(*) as qty'))
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $data = [];
        foreach ($rows as $row) {
            $data[] = (array) $row;
        }
        return $data;
    }

    /**
     * Punctuality for taking pill
     * @return array
     */
    public function all()
    {
        $rows = DB::table('transaction')
            ->select(DB::raw('DAY(last_consumed_datetime) as day, TIME_FORMAT(last_consumed_datetime , \'%H%i\') as time'))
            ->orderBy('day')
            ->orderBy('id')
            ->get();

        $data = [];
        foreach ($rows as $row) {
            $data[] = (array) $row;
        }
        return $data;
    }
}