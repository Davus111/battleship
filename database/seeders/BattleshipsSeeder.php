<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class BattleshipsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'triple_ship',
                'length' => 3,
            ],
            [
                'name' => 'double_ship',
                'length' => 2,
            ],
            [
                'name' => 'double_ship',
                'length' => 2,
            ],
            [
                'name' => 'single_ship',
                'length' => 1,
            ],
            [
                'name' => 'single_ship',
                'length' => 1,
            ],
            [
                'name' => 'single_ship',
                'length' => 1,
            ],
            [
                'name' => 'single_ship',
                'length' => 1,
            ],
        ];

        DB::table('battleships')->insert($data);
    }
}
