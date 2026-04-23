<?php

namespace App\Repositories;

use App\Exceptions\WarehouseNotFoundException;
use App\Models\Warehousemodel;
use App\Repositories\Contracts\WarehouseRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class WarehouseRepository implements WarehouseRepositoryInterface
{
    public function findOrFail(int $id): Warehousemodel
    {
        try {
            return Warehousemodel::findOrFail($id);
        } catch (ModelNotFoundException) {
            throw WarehouseNotFoundException::withId($id);
        }
    }

    public function delete(Warehousemodel $warehouse): bool
    {
        return (bool) $warehouse->delete();
    }
}
