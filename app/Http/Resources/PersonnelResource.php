<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\RulResource;

class PersonnelResource extends JsonResource
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
            'avatar' => $this->avatar ? asset('storage/' . $this->avatar) : null,
            'name' => $this->name,
            'contact_number' => $this->contact_number,
            'serial_number' => $this->serial_number,
            'department' => $this->department,
            'rul' => RulResource::make($this->whenLoaded('rul')),
        ];
    }
}
