<?php

declare(strict_types=1);

namespace App\DTOs;

/**
 * total_price bu yerda yo'q - u har doim InformationItem::booted()
 * ichida quantity * item_price dan hisoblanadi. Client'dan kelgan
 * total_price qiymatiga arxitektura darajasida umuman ishonilmaydi.
 *
 * $id — faqat "sync" (update) senariysida ishlatiladi:
 *   - null bo'lsa => yangi item yaratiladi
 *   - to'ldirilgan bo'lsa => mavjud item yangilanadi
 */
final readonly class InformationItemData
{
    public function __construct(
        public ?int $id,
        public string $productName,
        public int $unitId,
        public float $quantity,
        public float $itemPrice,
    ) {
    }

    public static function fromArray(array $data, ?int $id = null): self
    {
        return new self(
            id: $id ?? (isset($data['id']) ? (int) $data['id'] : null),
            productName: $data['product_name'],
            unitId: (int) $data['unit_id'],
            quantity: (float) $data['quantity'],
            itemPrice: (float) $data['item_price'],
        );
    }
}
