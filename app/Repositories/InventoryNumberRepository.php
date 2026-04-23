<?php

namespace App\Repositories;

use App\Models\Inventorynumbermodel;
use App\Repositories\Contracts\InventoryNumberRepositoryInterface;

class InventoryNumberRepository implements InventoryNumberRepositoryInterface
{
    public function create(int $number): Inventorynumbermodel
    {
        return Inventorynumbermodel::create([
            'inventory_number' => $number,
        ]);
    }
}
