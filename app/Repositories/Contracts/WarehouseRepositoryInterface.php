<?php

namespace App\Repositories\Contracts;

use App\Models\WarehouseModel;

interface WarehouseRepositoryInterface
{
    public function findOrFail(int $id): WarehouseModel;
    public function delete(WarehouseModel $warehouse): bool;
}
