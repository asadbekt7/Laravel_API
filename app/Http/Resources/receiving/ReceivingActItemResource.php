<?php

declare(strict_types=1);

namespace App\Http\Resources\Receiving;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\DTO\Receiving\ReceivingActItemData
 */
class ReceivingActItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'row_number'   => $this->rowNumber,
            'product_name' => $this->productName,
            'unit'         => $this->unitName,
            'quantity'     => $this->quantity,
            'price'        => $this->price,
            'sum'          => $this->sum,
        ];
    }
}
