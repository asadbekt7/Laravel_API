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
            'id'               => $this->id,
            'information_item' => new InformationItemResource($this->whenLoaded('informationItem')),
            'type'             => $this->whenLoaded('type'),
            'category'         => $this->whenLoaded('category'),
            'model'            => $this->whenLoaded('model'),

            // YANGI: faqat "warehouse" relatsiyasi eager-load qilingan joyda (umumiy
            // ro'yxat, WarehouseItemsController) chiqadi. Bitta akt ichidagi
            // ro'yxatda (WarehouseController::show) bu maydon ko'rinmaydi,
            // chunki u yerda warehouse allaqachon ma'lum.
            'warehouse' => $this->whenLoaded('warehouse', fn () => [
                'id'         => $this->warehouse->id,
                'akt_number' => $this->warehouse->akt_number,
                'akt_date'   => $this->warehouse->akt_date?->format('Y-m-d'),
                'location'   => $this->warehouse->location?->name,
            ]),
        ];
    }
}
