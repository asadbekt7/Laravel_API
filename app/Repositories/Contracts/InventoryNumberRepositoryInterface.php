<?php

namespace App\Repositories\Contracts;

use App\Models\Inventorynumbermodel;

interface InventoryNumberRepositoryInterface
{
    public function create(int $number): Inventorynumbermodel;
}
