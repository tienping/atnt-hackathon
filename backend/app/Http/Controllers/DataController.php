<?php
/**
 * @author Hermo Developer <dev@hermo.my>
 * @link http://www.hermo.my
 * @copyright Copyright (c) Hermo Creative Sdn. Bhd.
 */

namespace App\Http\Controllers;

use App\Transaction;
use App\Pill;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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

    public function weight($datetime, $weight)
    {
        $apiKey = '478cd65a29a90859d5ddcafddc154563';
        $deviceId  = 'debe1019583e092990746a3c28c18d47';

        $curl = curl_init();

        $timestamp = Carbon::createFromFormat('YmdHi', $datetime)->toIso8601String();
        // dd($timestamp);
        //dd(Carbon::createFromFormat('YmdHi', $datetime)->format('Y-m-d H:i'));
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api-m2x.att.com/v2/devices/debe1019583e092990746a3c28c18d47/streams/weight/value",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_POSTFIELDS => "{\n\t\"timestamp\": \"{$timestamp}\",\n\t\"value\": \"{$weight}\"\n}",
            CURLOPT_HTTPHEADER => array(
                "content-type: application/json",
                "x-m2x-key: 478cd65a29a90859d5ddcafddc154563"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $previousTransation = Transaction::whereTimetableId(1)->orderBy('id', 'DESC')->first();
            $pill = Pill::find(1);
            if (!$previousTransation) {
                $pill = Pill::find(1);
                $weight_taken = ($pill->weight_value * $pill->qty) - $weight; // 1000 - 980 = 20
                $qty_taken = $weight_taken / $pill->weight_value; // 1000 - 980 =  20 / 10 = 2
            } else {
                $weight_taken = $previousTransation->last_weight_value - $weight; // 980 - 980 = 0
                $qty_taken = $weight_taken / $pill->weight_value; // 980 - 980 =  0 / 10 = 0
            }

            $data = [
                'timetable_id' => 1,
                'pill_id' => 1,
                'last_consumed_datetime' => Carbon::createFromFormat('YmdHi', $datetime)->format('Y-m-d H:i'),
                'last_weight_value' => $weight,
                'last_consumed_qty' => $weight / $pill->weight_value,
                'weight_taken' => $weight_taken,
                'qty_taken' => $qty_taken
            ];
            Transaction::insert($data);

            echo $response;
        }
    }

    public function check($value='')
    {
        # code...
    }

    public function transactions()
    {
        return response()->json(Transaction::all());
    }

    public function quantities()
    {   
        return response()->json(DB::table('transaction')
                ->select(DB::raw('DATE(last_consumed_datetime) as date'), DB::raw('count(*) as count'))
                ->groupBy('date')->get());
    }

    public function weights()
    {
        return response()->json(
                DB::table('transaction')
                ->select(DB::raw('DATE(last_consumed_datetime) as date'), DB::raw('sum(weight_taken) as weight_taken'))
                ->groupBy('date')->get());
    }

    // public function times()
    // {
    //     return response()->json(
    //             DB::table('transaction')
    //             ->select(DB::raw('EXTRACT(DAY_HOUR FROM last_consumed_datetime) as day_hour'), DB::raw('sum(weight_taken) as weight_taken'))->get());
    // }   
}