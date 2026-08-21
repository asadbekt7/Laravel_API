<?php

declare(strict_types=1);

namespace App\DTOs\Receiving;

use App\Models\InformationModel;

/**
 * Akt jadvalidagi bitta qatorni (bitta mahsulotni) ifodalovchi o'zgarmas DTO.
 * Pul bilan bog'liq maydonlar har doim string (decimal) ko'rinishida saqlanadi —
 * float ishlatilmaydi, chunki float yig'indilarda dumaloqlash xatosi berishi mumkin.
 */
final readonly class ReceivingActItemData
{
    public function __construct(
        public int $rowNumber,
        public string $productName,
        public string $unitName,
        public int $quantity,
        public string $price,
        public string $sum,
    ) {
    }

    public static function fromModel(InformationModel $information, int $rowNumber): self
    {
        // bcmath — decimal(20,2) ustida aniq (float'siz) arifmetika uchun.
        $sum = bcmul(
            (string) $information->quantity,
            (string) $information->price,
            2
        );

        return new self(
            rowNumber: $rowNumber,
            productName: $information->product_name,
            unitName: $information->unit?->name ?? '',
            quantity: $information->quantity,
            price: number_format((float) $information->price, 2, '.', ''),
            sum: $sum,
        );
    }
}
