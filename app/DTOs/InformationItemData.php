<?php

declare(strict_types=1);

namespace App\DTO;

/**
 * total_price bu yerda property emas - u har doim totalPrice() orqali
 * quantity * item_price dan hisoblanadi. Client'dan kelgan total_price
 * qiymatiga arxitektura darajasida ishonilmasligi shu bilan kafolatlanadi.
 */
final class InformationItemData
{
    public function __construct(
        public readonly string $productName,
        public readonly int $unitId,
        public readonly float $quantity,
        public readonly float $itemPrice,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            productName: $data['product_name'],
            unitId: (int) $data['unit_id'],
            quantity: (float) $data['quantity'],
            itemPrice: (float) $data['item_price'],
        );
    }

    public function totalPrice(): float
    {
        return round($this->quantity * $this->itemPrice, 2);
    }
}
