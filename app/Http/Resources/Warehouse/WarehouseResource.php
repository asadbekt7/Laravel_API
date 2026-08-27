<?php

declare(strict_types=1);

namespace App\Http\Resources\Warehouse;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'akt_number'   => $this->akt_number,
            'akt_date'     => $this->akt_date?->toDateString(),
            'status'       => $this->status->value,
            'status_label' => $this->status->label(),
            'description'  => $this->description,
            'location'     => $this->whenLoaded('location'),
            'assignee'     => $this->whenLoaded('assignee'),
            'information'  => $this->whenLoaded('information'),
            'items'        => WarehouseItemResource::collection($this->whenLoaded('items')),
            'created_at'   => $this->created_at?->toDateTimeString(),
        ];
    }
}
