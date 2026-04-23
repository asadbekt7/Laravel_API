<?php

namespace App\Services\Contracts;

use App\Models\Computermodel;

interface ComputerTransferServiceInterface
{
    public function transfer(int $warehouseId, int $inventoryNumber, string $roomName): Computermodel;
}
