<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WasteTypeSeeder extends Seeder
{
    const TABLE_NAME = 'waste_type';
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'waste_type_name' => 'BIO DEGRADABLE',
                'created_by' => 1,
                'updated_at' => Carbon::now(),
                'created_at' => Carbon::now(),
            ],
            [
                'waste_type_name' => 'NON BIO DEGRADABLE',
                'created_by' => 1,
                'updated_at' => Carbon::now(),
                'created_at' => Carbon::now(),
            ],
            [
                'waste_type_name' => 'HAZARDOUS',
                'created_by' => 1,
                'updated_at' => Carbon::now(),
                'created_at' => Carbon::now(),
            ]
        ];

        foreach ($data as $userData) {
            DB::table(self::TABLE_NAME)->insert($userData);
        }
    }
}
