<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UsersSeeder extends Seeder
{
    const TABLE_NAME = 'users';
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'firstname' => 'John Joseph',
                'lastname' => 'Banaja',
                'middlename' => '-',
                'fullname' => 'John Joseph Banaja',
                //'address' => 'Barangay Sampinit, Bago City',
                'position' => 'CENRO OFFICER',
                'designation' => 'admin',
                'username' => 'johnjoseph',
                'email' => 'jj@gmail.com',
                'is_active' => true,
                'password' => Hash::make('cenroadmin1'),
                'email_verified_at' => now(),
                'updated_at' => Carbon::now(),
                'created_at' => Carbon::now(),
            ]
        ];

        foreach ($data as $userData) {
            DB::table(self::TABLE_NAME)->insert($userData);
        }
    }
}
