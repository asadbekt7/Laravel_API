<?php

namespace App\Repositories;

use App\Exceptions\WarehouseNotFoundException;
use App\Models\WarehouseModel;
use App\Repositories\Contracts\WarehouseRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class WarehouseRepository implements WarehouseRepositoryInterface
{
    public function findOrFail(int $id): WarehouseModel
    {
        try {
            return WarehouseModel::findOrFail($id);
        } catch (ModelNotFoundException) {
            throw WarehouseNotFoundException::withId($id);
        }
    }
    public function delete(WarehouseModel $warehouse): bool
    {
        return (bool) $warehouse->delete();
    }
}
