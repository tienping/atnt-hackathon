<?php

use Illuminate\Database\Seeder;

class PillTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('pill')->truncate();
        DB::table('pill')->insert([
            'name' => 'Chlorothiazide',
            'sku' => 'chlorothiazide@diuril',
            'weight_value' => '700',
            'qty' => '60',
        ]);
    }
}
