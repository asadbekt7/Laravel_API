<?php
// App/Services/Contracts/DeviceTransferServiceInterface.php
namespace App\Services\Contracts;

use App\Models\Devicemodel;

interface DeviceTransferServiceInterface
{
    public function transfer(
        int $warehouseId,
        int $inventoryNumber,
        string $roomName
    ): Devicemodel;
}
