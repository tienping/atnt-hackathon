<?php

use Illuminate\Database\Seeder;

class TimetableTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('timetable')->truncate();
        DB::table('timetable')->insert([
            'pill_id' => '1',
            'consume_time' => '1400',
            'qty' => '1',
        ]);
    }
}
