<?php

namespace Database\Seeders;

use App\Models\Ics211Record;
use App\Models\IcsLog;
use App\Models\Rul;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class IcsLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $records = Ics211Record::with('operators')->get();
        $rulIds = Rul::pluck('id')->toArray();

        if ($records->isEmpty() || empty($rulIds)) {
            return;
        }

        $actions = [
            'created',
            'updated',
            'personnel_added',
            'status_changed',
        ];

        foreach ($records as $record) {
            $logCount = rand(2, 4);

            for ($i = 0; $i < $logCount; $i++) {
                $action = $actions[array_rand($actions)];
                $rulId = $record->operators->isNotEmpty()
                    ? $record->operators->random()->id
                    : $rulIds[array_rand($rulIds)];

                $description = match ($action) {
                    'created' => 'ICS 211 record created',
                    'updated' => 'ICS 211 record updated',
                    'personnel_added' => 'Personnel added to ICS',
                    'status_changed' => 'ICS status changed',
                    default => 'ICS activity logged',
                };

                IcsLog::create([
                    'uuid' => Str::uuid(),
                    'ics211_record_id' => $record->id,
                    'rul_id' => $rulId,
                    'action' => $action,
                    'description' => $description,
                    'old_values' => $action === 'updated' ? ['status' => 'pending'] : null,
                    'new_values' => $action === 'updated' ? ['status' => 'ongoing'] : null,
                    'created_at' => Carbon::now()->subDays(rand(0, 30)),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }
    }
}
