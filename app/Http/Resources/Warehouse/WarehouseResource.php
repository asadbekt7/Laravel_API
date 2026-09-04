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
            'status'       => $this->status?->value,
            'status_label' => $this->status?->label(),
            'description'  => $this->description,
            'location'     => $this->whenLoaded('location'),
            'assignee'     => $this->whenLoaded('assignee'),
            'information' => $this->whenLoaded('information', fn () => [
                'id'                   => $information->id,
                'name'                 => $information->name,
                'contract_number'      => $information->contract_number,
                'contract_date'        => $information->contract_date?->toDateString(),
                'hisob_faktura'        => $information->hisob_faktura,
                'hisob_faktura_date'   => $information->hisob_faktura_date?->toDateString(),
                'bildirishnoma_number' => $information->bildirishnoma_number,
                'bildirishnoma_date'   => $information->bildirishnoma_date?->toDateString(),
                'ishonchnoma_number'   => $information->ishonchnoma_number,
                'ishonchnoma_date'     => $information->ishonchnoma_date?->toDateString(),
                'supplier_id'          => $information->supplier_id,
                'supplier'             => $information->supplier?->name,
                'creator_id'           => $information->creator_id,
                'creator'              => $information->creator?->name,
                'description'          => $information->description,
                'status'               => $information->status,
                'files'                => $this->informationFiles($information),
            ]),
            'items'        => WarehouseItemResource::collection($this->whenLoaded('items')),
            'created_at'   => $this->created_at?->toDateTimeString(),
        ];
    }
}
