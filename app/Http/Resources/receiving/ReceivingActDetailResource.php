<?php

declare(strict_types=1);

namespace App\Http\Resources\Receiving;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * GET /api/receiving/{aktNumber} javobi.
 * Faqat blankda mavjud bo'lgan maydonlar qaytariladi — description, timestamp
 * kabi ortiqcha ma'lumotlar qasddan chiqarilmaydi (talab #7).
 *
 * @mixin \App\DTO\Receiving\ReceivingActData
 */
class ReceivingActDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'akt_number'         => $this->aktNumber,
            'akt_date'           => $this->aktDate,
            'hisob_faktura'      => $this->hisobFaktura,
            'hisob_faktura_date' => $this->hisobFakturaDate,
            'contract_number'    => $this->contractNumber,
            'contract_date'      => $this->contractDate,
            'supplier'           => $this->supplierName,
            'ishonchnoma_number' => $this->ishonchnomaNumber,
            'ishonchnoma_date'   => $this->ishonchnomaDate,
            'accepted_by'        => $this->assigneeName,
            'items'              => ReceivingActItemResource::collection($this->items),
            'total_sum'          => $this->totalSum,
            'total_sum_in_words' => $this->totalSumInWords,
        ];
    }
}
