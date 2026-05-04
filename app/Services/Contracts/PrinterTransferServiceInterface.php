<?php
// App/Services/Contracts/PrinterTransferServiceInterface.php
namespace App\Services\Contracts;

use App\Models\Printermodel;

interface PrinterTransferServiceInterface
{
    public function transfer(
        int $warehouseId,
        int $inventoryNumber,
        string $roomName
    ): Printermodel;
}
