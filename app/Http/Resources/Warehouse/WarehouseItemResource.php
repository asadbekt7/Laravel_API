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
        ];
    }
}
