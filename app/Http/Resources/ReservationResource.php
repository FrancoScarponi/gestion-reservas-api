<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "user" => $this->whenLoaded('user', function () {
                return [
                    "id" => $this->user->id,
                    "name" => $this->user->name,
                    "email" => $this->user->email,
                ];
            }),
            "workspace" => $this->whenLoaded('workspace', function () {
                return [
                    "id" => $this->workspace->id,
                    "name" => $this->workspace->name,
                    "location" => $this->workspace->location
                ];
            }),
            "status" => $this->status,
            "date" => $this->date,
            "start_time" => $this->start_time,
            "end_time" => $this->end_time,

        ];
    }
}
