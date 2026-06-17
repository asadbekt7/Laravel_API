<?php

namespace App\Http\Resources\Malumot;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MalumotResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            // Kontrakt
            'contract_number'         => $this->contract_number,
            'contract_date'           => $this->contract_date?->format('Y-m-d'),
            'contract_file_path'      => $this->contract_file_path,

            // Yetkazuvchi
            'yetkazuvchi_id'          => $this->yetkazuvchi_id,
            'yetkazuvchi'             => $this->whenLoaded('yetkazuvchi', fn() => [
                'id'         => $this->yetkazuvchi->id,
                'name'       => $this->yetkazuvchi->name,
                'INN_number' => $this->yetkazuvchi->INN_number,
            ]),

            // Bildirishnoma
            'bildirishnoma_number'    => $this->bildirishnoma_number,
            'bildirishnoma_date'      => $this->bildirishnoma_date?->format('Y-m-d'),
            'bildirishnoma_file_path' => $this->bildirishnoma_file_path,

            // Mahsulot
            'name'                    => $this->name,
            'unit_id'                 => $this->unit_id,
            'unit'                    => $this->whenLoaded('unit', fn() => [
                'id'   => $this->unit->id,
                'name' => $this->unit->name,
            ]),
            'quantity'                => $this->quantity,
            'price'                   => $this->price,
            'total_sum'               => round($this->quantity * $this->price, 2),

            // Ishonchnoma
            'ishonchnoma_number'      => $this->ishonchnoma_number,
            'ishonchnoma_date'        => $this->ishonchnoma_date?->format('Y-m-d'),
            'ishonchnoma_file_path'   => $this->ishonchnoma_file_path,

            // Hisob-faktura
            'hisob_faktura'           => $this->hisob_faktura,
            'hisob_faktura_date'      => $this->hisob_faktura_date?->format('Y-m-d'),
            'hisob_faktura_file_path' => $this->hisob_faktura_file_path,

            // Akt
            'akt_number'              => $this->akt_number,
            'akt_date'                => $this->akt_date?->format('Y-m-d'),

            // Qo'shimcha
            'description'             => $this->description,

            'created_at'              => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at'              => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
