<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CheckInDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'order_request_number' => $this->order_request_number,
            'checkin_date' => $this->checkin_date ? Carbon::parse($this->checkin_date)->format('Y-m-d') : null,
            'checkin_time' => $this->checkin_time ? Carbon::parse($this->checkin_time)->format('H:i') : null,
            'kind' => $this->kind,
            'category' => $this->category,
            'type' => $this->type,
            'resource_identifier' => $this->resource_identifier,
            'name_of_leader' => $this->name_of_leader,
            'contact_information' => $this->contact_information,
            'quantity' => $this->quantity,
            'department' => $this->department,
            'departure_point_of_origin' => $this->departure_point_of_origin,
            'departure_date' => $this->departure_date ? Carbon::parse($this->departure_date)->format('Y-m-d') : null,
            'departure_time' => $this->departure_time ? Carbon::parse($this->departure_time)->format('H:i') : null,
            'departure_method_of_travel' => $this->departure_method_of_travel,
            'with_manifest' => $this->with_manifest,
            'incident_assignment' => $this->incident_assignment,
            'other_qualifications' => $this->other_qualifications,
            'sent_resl' => $this->sent_resl,
            'status' => $this->status,
            'personnel' => new PersonnelResource($this->whenLoaded('personnel')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
