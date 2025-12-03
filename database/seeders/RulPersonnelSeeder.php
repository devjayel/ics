<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Rul;
use App\Models\Personnel;
use App\Models\Certificate;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Faker\Factory as Faker;

class RulPersonnelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        
        // Create storage directories if they don't exist
        Storage::disk('public')->makeDirectory('signatures');
        Storage::disk('public')->makeDirectory('certificates');

        $departments = [
            'BFP',
            'PNP',
            'Red Cross',
            'Search and Rescue',
            'Safety',
        ];

        foreach ($departments as $index => $department) {
            // Create RUL
            $rulUuid = Str::uuid();
            
            $rul = Rul::create([
                'uuid' => $rulUuid,
                'name' => $faker->name(),
                'contact_number' => '09' . rand(100000000, 999999999),
                'serial_number' => str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                'department' => $department,
                'token' => Str::random(60),
            ]);

            // Randomly add signature (70% chance)
            if (rand(1, 10) <= 7) {
                // Copy example signature from public assets
                $sourceSignature = public_path('images/example-signature.jpg');
                if (file_exists($sourceSignature)) {
                    $signaturePath = 'signatures/' . $rulUuid . '.jpg';
                    Storage::disk('public')->put($signaturePath, file_get_contents($sourceSignature));
                    $rul->update(['signature' => $signaturePath]);
                }
            }

            // Randomly add certificate (60% chance)
            if (rand(1, 10) <= 6) {
                // Copy example certificate from public assets
                $sourceCertificate = public_path('images/example-ceritificate.jpg');
                if (file_exists($sourceCertificate)) {
                    $certificatePath = 'certificates/' . time() . '_' . Str::uuid() . '.jpg';
                    Storage::disk('public')->put($certificatePath, file_get_contents($sourceCertificate));
                    
                    Certificate::create([
                        'uuid' => Str::uuid(),
                        'rul_id' => $rul->id,
                        'certificate_name' => 'Certificate - ' . $rul->name . '.jpg',
                        'file_path' => $certificatePath,
                    ]);
                }
            }

            // Create 3 Personnel for this RUL's department
            for ($i = 1; $i <= 3; $i++) {
                Personnel::create([
                    'uuid' => Str::uuid(),
                    'rul_id' => $rul->id,
                    'name' => $faker->name(),
                    'contact_number' => '09' . rand(100000000, 999999999),
                    'serial_number' => str_pad((($index * 3) + $i), 4, '0', STR_PAD_LEFT),
                    'department' => $department,
                    'token' => Str::random(60),
                ]);
            }
        }

        $this->command->info('✅ Created 5 RULs with signatures and certificates (randomized)');
        $this->command->info('✅ Created 15 Personnel (3 per department)');
    }
}
