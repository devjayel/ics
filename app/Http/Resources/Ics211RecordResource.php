<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Ics211RecordResource extends JsonResource
{
    /**
     * @property mixed $resource
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $isOperator = false;
        
        // Check if current user is an operator of this ICS record
        if ($request->user() && $this->relationLoaded('operators')) {
            $isOperator = $this->operators
                ->where('id', $request->user()->id)
                ->isNotEmpty();
        }

        return [
            'id' => $this->id,
            'token' => $this->token,
            'uuid' => $this->uuid,
            'order_request_number' => $this->order_request_number,
            'name' => $this->name,
            'type' => $this->type,
            'checkin_location' => $this->checkin_location,
            'region' => $this->region,
            'remarks' => $this->remarks,
            'remarks_image_attachment' => $this->remarks_image_attachment ? asset('storage/' . $this->remarks_image_attachment) : null, // Convert to full URL if exists
            'status' => $this->status,
            'is_operator' => $isOperator,
            'operators' => RulResource::collection($this->whenLoaded('operators')),
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
