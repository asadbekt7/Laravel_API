<?php

declare(strict_types=1);

namespace App\Http\Resources\Receiving;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * GET /api/receiving ro'yxatidagi bitta qator — akt_number bo'yicha guruhlangan agregat.
 *
 * @mixin \App\Models\InformationModel
 */
class ReceivingActListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'akt_number'      => $this->akt_number,
            'akt_date'        => $this->akt_date?->format('d.m.Y'),
            'contract_number' => $this->contract_number,
            'supplier'        => $this->supplier?->name,
            'items_count'     => (int) $this->items_count,
            'total_sum'       => number_format((float) $this->total_sum, 2, '.', ''),
        ];
    }
}
