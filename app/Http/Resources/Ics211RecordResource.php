<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Ics211RecordResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'start_date' => $this->start_date ? Carbon::parse($this->start_date)->format('Y-m-d') : null,
            'start_time' => $this->start_time ? Carbon::parse($this->start_time)->format('H:i') : null,
            'checkin_location' => $this->checkin_location,
            'remarks' => $this->remarks,
            'status' => $this->status,
            'rul_id' => $this->rul_id,
            'rul' => new RulResource($this->whenLoaded('rul')),
            'check_in_details' => CheckInDetailResource::collection($this->whenLoaded('checkInDetails')),
            'total_check_ins' => $this->when(
                $this->relationLoaded('checkInDetails'),
                fn() => $this->checkInDetails->count()
            ),
            'completed_check_ins' => $this->when(
                $this->relationLoaded('checkInDetails'),
                fn() => $this->checkInDetails->where('status', 'completed')->count()
            ),
            'pending_check_ins' => $this->when(
                $this->relationLoaded('checkInDetails'),
                fn() => $this->checkInDetails->where('status', 'pending')->count()
            ),
            'ongoing_check_ins' => $this->when(
                $this->relationLoaded('checkInDetails'),
                fn() => $this->checkInDetails->where('status', 'ongoing')->count()
            ),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
