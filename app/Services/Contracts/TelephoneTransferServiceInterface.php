<?php
// App/Services/Contracts/TelephoneTransferServiceInterface.php
namespace App\Services\Contracts;

use App\Models\Telephonemodel;

interface TelephoneTransferServiceInterface
{
    public function transfer(
        int $warehouseId,
        int $inventoryNumber,
        string $roomName
    ): Telephonemodel;
}
