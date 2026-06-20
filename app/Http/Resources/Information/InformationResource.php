<?php

namespace App\Http\Resources\Information;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InformationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                         => $this->id,

            // Asosiy ma'lumotlar
            'name'                       => $this->name,
            'description'                => $this->description,

            // Shartnoma
            'contract_number'            => $this->contract_number,
            'contract_date'              => $this->contract_date?->format('Y-m-d'),
            'contract_file_path'         => $this->contract_file_path,
            'contract_file_name'         => $this->contract_file_name,

            // Yetkazib beruvchi
            'supplier_id'                => $this->supplier_id,
            'supplier'                   => $this->whenLoaded('supplier', fn() => [
                'id'   => $this->supplier->id,
                'name' => $this->supplier->name,
            ]),

            // Bildirishnoma
            'bildirishnoma_number'       => $this->bildirishnoma_number,
            'bildirishnoma_date'         => $this->bildirishnoma_date?->format('Y-m-d'),
            'bildirishnoma_file_path'    => $this->bildirishnoma_file_path,
            'bildirishnoma_file_name'    => $this->bildirishnoma_file_name,

            // Mahsulot
            'product_name'               => $this->product_name,
            'quantity'                   => $this->quantity,
            'price'                      => $this->price,

            // O'lchov birligi
            'unit_id'                    => $this->unit_id,
            'unit'                       => $this->whenLoaded('unit', fn() => [
                'id'   => $this->unit->id,
                'name' => $this->unit->name,
            ]),

            // Ishonchnoma
            'ishonchnoma_number'         => $this->ishonchnoma_number,
            'ishonchnoma_date'           => $this->ishonchnoma_date?->format('Y-m-d'),
            'ishonchnoma_file_path'      => $this->ishonchnoma_file_path,
            'ishonchnoma_file_name'      => $this->ishonchnoma_file_name,

            // Hisob faktura
            'hisob_faktura'              => $this->hisob_faktura,
            'hisob_faktura_date'         => $this->hisob_faktura_date?->format('Y-m-d'),
            'hisob_faktura_file_path'    => $this->hisob_faktura_file_path,
            'hisob_faktura_file_name'    => $this->hisob_faktura_file_name,

            // Akt
            'akt_number'                 => $this->akt_number,
            'akt_date'                   => $this->akt_date?->format('Y-m-d'),

            // Timestamps
            'created_at'                 => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at'                 => $this->updated_at?->format('Y-m-d H:i:s'),
            'deleted_at'                 => $this->deleted_at?->format('Y-m-d H:i:s'),
        ];
    }
}
