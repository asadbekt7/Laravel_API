<?php
// App/Services/Contracts/NetworkDeviceTransferServiceInterface.php
namespace App\Services\Contracts;

use App\Models\NetworkDevicemodel;

interface NetworkDeviceTransferServiceInterface
{
    public function transfer(
        int $warehouseId,
        int $inventoryNumber,
        string $roomName
    ): NetworkDevicemodel;
}
