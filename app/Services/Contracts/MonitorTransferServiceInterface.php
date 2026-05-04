<?php
// App/Services/Contracts/MonitorTransferServiceInterface.php
namespace App\Services\Contracts;

use App\Models\Monitormodel;

interface MonitorTransferServiceInterface
{
    public function transfer(
        int $warehouseId,
        int $inventoryNumber,
        string $roomName
    ): Monitormodel;
}
