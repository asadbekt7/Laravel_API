<?php

declare(strict_types=1);

namespace App\DTOs\Warehouse;

final readonly class WarehouseAcceptData
{
    /**
     * @param  WarehouseItemClassificationData[]  $items
     */
    public function __construct(
        public string $aktNumber,
        public string $aktDate,
        public int $locationId,
        public ?string $description,
        public array $items,
    ) {
    }
}
