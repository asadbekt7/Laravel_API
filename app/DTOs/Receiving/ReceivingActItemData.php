<?php

declare(strict_types=1);

namespace App\DTOs\Receiving;

use App\Models\InformationItem;

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
        public string $quantity,
        public string $price,
        public string $sum,
    ) {
    }

    public static function fromModel(InformationItem $item, int $rowNumber): self
    {
        // bcmath — decimal(20,2) ustida aniq (float'siz) arifmetika uchun.
        $sum = bcmul((string) $item->quantity, (string) $item->item_price, 2);

        return new self(
            rowNumber: $rowNumber,
            productName: $item->product_name,
            unitName: $item->unit?->name ?? '',
            quantity: (string) $item->quantity,
            price: number_format((float) $item->item_price, 2, '.', ''),
            sum: $sum,
        );
    }
}
