<?php

namespace Database\Seeders;

use App\Models\CheckInDetails;
use App\Models\Ics211Record;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class IcsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $operations = [
            ['name' => 'Wildfire Response Operation', 'location' => 'Northern Forest Area', 'month' => 1],
            ['name' => 'Flood Relief Mission', 'location' => 'Riverside District', 'month' => 2],
            ['name' => 'Search and Rescue Operation', 'location' => 'Mountain Ridge Area', 'month' => 3],
            ['name' => 'Earthquake Response Mission', 'location' => 'Downtown Urban Area', 'month' => 4],
            ['name' => 'Medical Emergency Response', 'location' => 'Central Hospital District', 'month' => 5],
            ['name' => 'Storm Damage Assessment', 'location' => 'Coastal Region', 'month' => 6],
            ['name' => 'Hazmat Incident Response', 'location' => 'Industrial Zone', 'month' => 7],
            ['name' => 'Evacuation Coordination', 'location' => 'Suburban Communities', 'month' => 8],
            ['name' => 'Forest Fire Prevention', 'location' => 'Western Wilderness', 'month' => 9],
            ['name' => 'Hurricane Preparedness', 'location' => 'Southern Coastal Area', 'month' => 10],
            ['name' => 'Winter Storm Response', 'location' => 'Highland Region', 'month' => 11],
            ['name' => 'Mass Casualty Incident', 'location' => 'City Center', 'month' => 12],
        ];

        $personnelPositions = [
            'Operations Section Chief',
            'Planning Section Chief',
            'Logistics Section Chief',
            'Finance Section Chief',
            'Safety Officer',
            'Liaison Officer',
            'Public Information Officer',
            'Medical Unit Leader',
            'Communications Unit Leader',
            'Field Team Leader',
        ];

        $equipmentTypes = [
            ['name' => 'Mobile Command Unit', 'type' => 'Heavy Vehicle', 'position' => 'Logistics Section'],
            ['name' => 'Emergency Generator', 'type' => 'Power Equipment', 'position' => 'Facilities Unit'],
            ['name' => 'Communication System', 'type' => 'Radio Equipment', 'position' => 'Communications Unit'],
            ['name' => 'Medical Supply Kit', 'type' => 'Medical Equipment', 'position' => 'Medical Unit'],
            ['name' => 'Water Tanker', 'type' => 'Heavy Vehicle', 'position' => 'Supply Unit'],
            ['name' => 'Rescue Equipment Set', 'type' => 'Rescue Gear', 'position' => 'Operations Section'],
        ];

        $statuses = ['completed', 'ongoing', 'pending'];
        $departments = ['Fire Operations', 'Medical Services', 'Logistics Support', 'Search and Rescue', 'Emergency Management'];

        foreach ($operations as $index => $operation) {
            $month = $operation['month'];
            $startDate = "2025-{$month}-" . rand(1, 28);
            
            // Create ICS 211 Record
            $icsRecord = Ics211Record::create([
                'uuid' => Str::uuid(),
                'rul_id' => 1,
                'name' => $operation['name'],
                'start_date' => $startDate,
                'start_time' => sprintf('%02d:00:00', rand(6, 14)),
                'checkin_location' => $operation['location'] . ' Command Center',
                'remarks' => 'Priority response operation for ' . strtolower($operation['name']),
                'status' => $statuses[array_rand($statuses)],
                'created_at' => $startDate . ' ' . sprintf('%02d:00:00', rand(6, 14)),
                'updated_at' => $startDate . ' ' . sprintf('%02d:30:00', rand(15, 23)),
            ]);

            // Create 3-5 personnel check-ins
            $personnelCount = rand(3, 5);
            $usedPersonnelIds = [];
            
            for ($i = 0; $i < $personnelCount; $i++) {
                do {
                    $personnelId = rand(1, 20);
                } while (in_array($personnelId, $usedPersonnelIds));
                
                $usedPersonnelIds[] = $personnelId;
                
                $checkInTime = sprintf('%02d:%02d:00', rand(6, 12), rand(0, 59));
                $etd = sprintf('%02d:%02d:00', rand(6, 9), rand(0, 59));
                $eta = sprintf('%02d:%02d:00', intval(substr($etd, 0, 2)) + 1, rand(0, 59));
                
                CheckInDetails::create([
                    'uuid' => Str::uuid(),
                    'personnel_id' => $personnelId,
                    'ics211_record_id' => $icsRecord->id,
                    'order_request_number' => 'REQ-' . $startDate . '-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                    'checkin_date' => $startDate,
                    'checkin_time' => $checkInTime,
                    'kind' => 'Overhead',
                    'category' => 'Personnel',
                    'type' => 'Person',
                    'resource_identifier' => 'PERS-' . str_pad($personnelId, 4, '0', STR_PAD_LEFT),
                    'name_of_leader' => 'Team Leader ' . chr(65 + $i),
                    'contact_information' => '+1-555-0' . rand(100, 999),
                    'quantity' => 1,
                    'department' => $departments[array_rand($departments)],
                    'departure_point_of_origin' => 'Station ' . rand(1, 10),
                    'departure_date' => $startDate,
                    'departure_time' => $etd,
                    'departure_method_of_travel' => ['Ground Vehicle', 'Air Transport', 'Emergency Vehicle'][rand(0, 2)],
                    'with_manifest' => rand(0, 1),
                    'incident_assignment' => $personnelPositions[array_rand($personnelPositions)],
                    'other_qualifications' => 'Certified emergency responder with ' . rand(2, 10) . ' years experience',
                    'sent_resl' => rand(0, 1),
                    'status' => $statuses[array_rand($statuses)],
                    'created_at' => $startDate . ' ' . $checkInTime,
                ]);
            }

            // Create 2-3 equipment check-ins
            $equipmentCount = rand(2, 3);
            $usedEquipment = [];
            
            for ($i = 0; $i < $equipmentCount; $i++) {
                do {
                    $equipmentIndex = rand(0, count($equipmentTypes) - 1);
                } while (in_array($equipmentIndex, $usedEquipment));
                
                $usedEquipment[] = $equipmentIndex;
                $equipment = $equipmentTypes[$equipmentIndex];
                
                $checkInTime = sprintf('%02d:%02d:00', rand(6, 12), rand(0, 59));
                $etd = sprintf('%02d:%02d:00', rand(6, 9), rand(0, 59));
                $eta = sprintf('%02d:%02d:00', intval(substr($etd, 0, 2)) + 1, rand(0, 59));
                
                CheckInDetails::create([
                    'uuid' => Str::uuid(),
                    'personnel_id' => null,
                    'ics211_record_id' => $icsRecord->id,
                    'order_request_number' => 'EQ-' . $startDate . '-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                    'checkin_date' => $startDate,
                    'checkin_time' => $checkInTime,
                    'kind' => 'Equipment',
                    'category' => 'Equipment',
                    'type' => $equipment['type'],
                    'resource_identifier' => 'EQ-' . strtoupper(substr($equipment['name'], 0, 3)) . '-' . rand(100, 999),
                    'name_of_leader' => 'Equipment Operator ' . chr(65 + $i),
                    'contact_information' => '+1-555-0' . rand(100, 999),
                    'quantity' => rand(1, 3),
                    'department' => $departments[array_rand($departments)],
                    'departure_point_of_origin' => ['Main Depot', 'Storage Facility', 'Equipment Yard'][rand(0, 2)],
                    'departure_date' => $startDate,
                    'departure_time' => $etd,
                    'departure_method_of_travel' => $equipment['type'],
                    'with_manifest' => 1,
                    'incident_assignment' => $equipment['position'],
                    'other_qualifications' => 'Equipment inspection completed, fully operational',
                    'sent_resl' => rand(0, 1),
                    'status' => $statuses[array_rand($statuses)],
                    'created_at' => $startDate . ' ' . $checkInTime,
                ]);
            }
        }
    }
}
