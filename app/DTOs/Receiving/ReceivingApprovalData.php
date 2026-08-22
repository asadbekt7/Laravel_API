<?php

declare(strict_types=1);

namespace App\DTOs\Receiving;

final readonly class ReceivingApprovalData
{
    public function __construct(
        public int $rowNumber,
        public string $fullName,
        public ?string $position,
        public string $statusKey,
        public ?string $date,
        public ?string $time,
    ) {
    }
}
