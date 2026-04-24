<?php

namespace App\Repositories\Contracts;

use App\Models\Warehousemodel;

interface WarehouseRepositoryInterface
{
    public function findOrFail(int $id): Warehousemodel;
    public function delete(Warehousemodel $warehouse): bool;
}
