<?php

declare(strict_types=1);

namespace App\Http\Resources\Warehouse;

use App\Http\Resources\Information\InformationItemResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'information_item' => $this->whenLoaded('informationItem', function ($informationItem) {
                return $informationItem
                    ? new InformationItemResource($informationItem)
                    : null;
            }),

            'type'     => $this->whenLoaded('type'),
            'category' => $this->whenLoaded('category'),
            'model'    => $this->whenLoaded('model'),

            'quantity' => (float) $this->quantity,

            'created_at' => $this->created_at?->toDateTimeString(),

            'asset_type'              => $this->asset_type,
            'responsible_person_id'   => $this->responsible_person_id,
            'responsible_person_name' => $this->responsible_person_name,

            'tmz_id' => $this->tmz_id,
            'tmz'    => $this->whenLoaded('tmz'),

            'warehouse' => $this->whenLoaded('warehouse', function ($warehouse) {
                return $warehouse ? [
                    'id'         => $warehouse->id,
                    'akt_number' => $warehouse->akt_number,
                    'akt_date'   => $warehouse->akt_date?->format('Y-m-d'),
                    'location'   => $warehouse->location?->name,
                ] : null;
            }),
        ];
    }
}
