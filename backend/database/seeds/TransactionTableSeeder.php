<?php

use Illuminate\Database\Seeder;

class TransactionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('transaction')->truncate();
        DB::table('transaction')->insert([
            'timetable_id' => '1',
            'pill_id' => '1',
            'last_consumed_datetime' => '2017-07-01 13:05:00',
            'last_weight_value' => '41300',
            'last_consumed_qty' => '59',
            'weight_taken' => '700',
            'qty_taken' => '1',
        ]);
    }
}
