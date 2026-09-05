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

            'quantity'         => (float) $this->quantity,

            'created_at'       => $this->created_at?->toDateTimeString(),

            // YANGI:
            'asset_type'              => $this->asset_type,
            'responsible_person_id'   => $this->responsible_person_id,
            'responsible_person_name' => $this->responsible_person_name,

            // Faqat asset_type = 'tmz' bo'lganda mazmunli, aks holda null bo'ladi.
            // 'tmz' relatsiyasi eager-load qilingan joyda (masalan
            // AcceptInformationToWarehouseAction, WarehouseItemService) to'liq
            // obyekt sifatida chiqadi, aks holda faqat tmz_id ko'rinadi.
            'tmz_id' => $this->tmz_id,
            'tmz'    => $this->whenLoaded('tmz'),

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
