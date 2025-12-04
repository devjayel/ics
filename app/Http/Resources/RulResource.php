<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RulResource extends JsonResource
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
            'contact_number' => $this->contact_number,
            'serial_number' => $this->serial_number,
            'department' => $this->department,
            'signature' => $this->signature ? asset('storage/' . $this->signature) : null,
            'certificates' => CertificateResource::collection($this->whenLoaded('certificates')),
        ];
    }
}
