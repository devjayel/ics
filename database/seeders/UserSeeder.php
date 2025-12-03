<?php

namespace Database\Seeders;

use App\Models\Personnel;
use App\Models\Rul;
use App\Models\User;
use App\Models\Certificate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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
        //Create a signature and certificate for the sample RUL
        $rulUuid = Str::uuid();

        $rul = Rul::create([
            'uuid' => $rulUuid,
            'name' => 'Bob Smith',
            'contact_number' => '123-456-7890',
            'serial_number' => '111',
            'department' => 'Beureau of Fire Protection',
        ]);

        // Copy signature from public assets
        $sourceSignature = public_path('images/example-signature.jpg');
        if (file_exists($sourceSignature)) {
            $signaturePath = 'signatures/' . $rulUuid . '.jpg';
            Storage::disk('public')->put($signaturePath, file_get_contents($sourceSignature));
            $rul->update(['signature' => $signaturePath]);
        }

        // Copy certificate from public assets
        $sourceCertificate = public_path('images/example-ceritificate.jpg');
        if (file_exists($sourceCertificate)) {
            $certificatePath = 'certificates/' . time() . '_' . $rulUuid . '.jpg';
            Storage::disk('public')->put($certificatePath, file_get_contents($sourceCertificate));
            
            Certificate::create([
                'uuid' => Str::uuid(),
                'rul_id' => $rul->id,
                'certificate_name' => 'Certificate - ' . $rul->name . '.jpg',
                'file_path' => $certificatePath,
            ]);
        }

        Log::info('RUL Seeder: Sample RUL created successfully.');
        //Create sample Personnel
        Personnel::create([
            'uuid' => Str::uuid(),
            'rul_id' => $rul->id,
            'name' => 'Alice Johnson',
            'contact_number' => '987-654-3210',
            'serial_number' => '222',
            'department' => 'Red Cross',
        ]);
        Personnel::create([
            'uuid' => Str::uuid(),
            'rul_id' => $rul->id,
            'name' => 'John Martinez',
            'contact_number' => '09171234567',
            'serial_number' => '223',
            'department' => 'Red Cross',
        ]);
        Personnel::create([
            'uuid' => Str::uuid(),
            'rul_id' => $rul->id,
            'name' => 'Maria Santos',
            'contact_number' => '09189876543',
            'serial_number' => '224',
            'department' => 'Red Cross',
        ]);
        Personnel::create([
            'uuid' => Str::uuid(),
            'rul_id' => $rul->id,
            'name' => 'Carlos Reyes',
            'contact_number' => '09195551234',
            'serial_number' => '225',
            'department' => 'Red Cross',
        ]);
        Personnel::create([
            'uuid' => Str::uuid(),
            'rul_id' => $rul->id,
            'name' => 'Sarah Garcia',
            'contact_number' => '09203334455',
            'serial_number' => '226',
            'department' => 'Red Cross',
        ]);
    }
}
