<?php

declare(strict_types=1);

namespace App\DTOs\Warehouse;

final readonly class WarehouseUpdateData
{
    public function __construct(
        public int $aktNumber,
        public string $aktDate,
    ) {
    }
}
