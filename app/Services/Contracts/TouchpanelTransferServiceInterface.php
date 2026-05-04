<?php
// App/Services/Contracts/TouchpanelTransferServiceInterface.php
namespace App\Services\Contracts;

use App\Models\Touchpanelmodel;

interface TouchpanelTransferServiceInterface
{
    public function transfer(
        int $warehouseId,
        int $inventoryNumber,
        string $roomName
    ): Touchpanelmodel;
}
