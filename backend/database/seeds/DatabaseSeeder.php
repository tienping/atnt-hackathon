<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//         $this->call(PillTableSeeder::class);
//         $this->call(TimetableTableSeeder::class);
         $this->call(TransactionTableSeeder::class);
    }
}
