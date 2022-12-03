<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PettycashGlCcSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('pettycash_cc_gls')->insert([
            [
                'company_id' => 1,
                'gl' => '70020101',
                'cc' => 'C1200002',
                'privilege' => 0
            ],
            [
                'company_id' => 1,
                'gl' => '52150106',
                'cc' => 'C1100002',
                'privilege' => 0
            ],
            [
                'company_id' => 1,
                'gl' => '52030101',
                'cc' => 'C1100003',
                'privilege' => 1
            ],
        ]);
    }
}
