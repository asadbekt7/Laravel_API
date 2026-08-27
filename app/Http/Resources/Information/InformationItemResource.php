<?php

declare(strict_types=1);

namespace App\Http\Resources\Information;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InformationItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'product_name' => $this->product_name,
            'unit'         => $this->whenLoaded('unit'),
            'quantity'     => (float) $this->quantity,
            'item_price'   => (float) $this->item_price,
            'total_price'  => (float) $this->total_price,
        ];
    }
}
