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

    public function take($timestamp, $value)
    {
        $apiKey = '478cd65a29a90859d5ddcafddc154563';
        $deviceId  = 'debe1019583e092990746a3c28c18d47';

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api-m2x.att.com/v2/devices/debe1019583e092990746a3c28c18d47/streams/weight2/value",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_POSTFIELDS => "{\n\t\"timestamp\": \"{$timestamp}\",\n\t\"value\": \"{$value}\"\n}",
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
          echo $response;
        }
    }
}