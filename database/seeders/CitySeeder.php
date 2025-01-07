<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CitySeeder extends Seeder
{
    const TABLE_NAME = 'city';
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'city_name' => 'Bago City',
                'latitude' => 10.503405,
                'longitude' => 122.966301,
                'updated_at' => Carbon::now(),
                'created_at' => Carbon::now(),
            ]
        ];

        foreach ($data as $userData) {
            DB::table(self::TABLE_NAME)->insert($userData);
        }
    }
}
