<?php

declare(strict_types=1);

namespace App\Http\Resources\Warehouse;

use App\Http\Resources\Concerns\FormatsInformationFiles;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseListResource extends JsonResource
{
    use FormatsInformationFiles;

    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'akt_number'      => $this->akt_number,
            'akt_date'        => $this->akt_date?->format('d.m.Y'),
            'contract_number' => $this->information?->contract_number,
            'supplier'        => $this->information?->supplier?->name,
            'items_count'     => (int) $this->items_count,
            'items_total'     => (float) $this->items_total,

            // YANGI: information'ga tegishli barcha hujjat fayllari.
            'files' => $this->informationFiles($this->information),
            'pdf_url' => route('warehouse.receiving.pdf', $this->id),
        ];
    }
}
