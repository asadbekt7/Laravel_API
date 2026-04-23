<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class WarehouseNotFoundException extends Exception
{
    public function __construct(
        string $message = 'Warehouse yozuvi topilmadi.',
        int $code = 404,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
    public static function withId(int $warehouseId): static
    {
        return new static("ID={$warehouseId} bo'lgan warehouse yozuvi topilmadi.", 404);
    }
}
