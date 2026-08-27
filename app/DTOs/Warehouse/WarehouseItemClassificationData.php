<?php

declare(strict_types=1);

namespace App\DTOs\Warehouse;

final readonly class WarehouseItemClassificationData
{
    public function __construct(
        public int $informationItemId,
        public int $typeId,
        public ?int $categoryId,
        public ?int $modelId,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            informationItemId: (int) $data['information_item_id'],
            typeId: (int) $data['type_id'],
            categoryId: isset($data['category_id']) ? (int) $data['category_id'] : null,
            modelId: isset($data['model_id']) ? (int) $data['model_id'] : null,
        );
    }
}
