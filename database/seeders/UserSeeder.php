<?php

namespace Database\Seeders;

use App\Models\Personnel;
use App\Models\Rul;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'uuid' => Str::uuid(),
                'name' => "JL Romero Juanitas",
                'email' => "juanitas@ics.com",
                'password' => Hash::make("password"),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid(),
                'name' => "Jesuit Carillo",
                'email' => "carillo@ics.com",
                'password' => Hash::make("password"),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid(),
                'name' => "Kim Berango Alandy-Dy",
                'email' => "kim@ics.com",
                'password' => Hash::make("password"),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid(),
                'name' => "Jardine Davies Camena",
                'email' => "jardine@ics.com",
                'password' => Hash::make("password"),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid(),
                'name' => "China Tablizo",
                'email' => "tablizo@ics.com",
                'password' => Hash::make("password"),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        User::insert($users);
        Log::info('==============================================');
        Log::info('UserSeeder: Users seeded successfully.');
        foreach ($users as $user) {
            Log::info('Name: ' . $user['name'] . ', Email: ' . $user['email']);
        }
        Log::info('==============================================');
        //Create a sample RUL
        Rul::create([
            'uuid' => Str::uuid(),
            'name' => 'Bob Smith',
            'contact_number' => '123-456-7890',
            'serial_number' => '111',
            'department' => 'Beureau of Fire Protection',
        ]);
        Log::info('RUL Seeder: Sample RUL created successfully.');
        //Create a sample Personnel
        Personnel::create([
            'uuid' => Str::uuid(),
            'name' => 'Alice Johnson',
            'contact_number' => '987-654-3210',
            'serial_number' => '222',
            'department' => 'Red Cross',
        ]);
    }
}
