<?php
// app/Services/WarehouseService.php

namespace App\Services;

use App\Models\Warehousemodel;
use Illuminate\Support\Facades\DB;

class WarehouseService
{
    public function createBulk(array $data): array
    {
        $records = [];

        DB::transaction(function () use ($data, &$records) {
            for ($i = 1; $i <= $data['quantity']; $i++) {
                $record = Warehousemodel::create([
                    'name'          => $data['name'] . '-' . $i,
                    'category_id' => $data['category_id'],
                    'model_id'     => $data['model_id'],
                    'unit_id'      => $data['unit_id'],
                    'quantity'      => 1,
                    'description'   => $data['description'],
                ]);

                $records[] = $record->load(['category', 'model', 'unit']);
            }
        });

        return $records;
    }

    public function getAll(int $perPage = 15)
    {
        return Warehousemodel::with(['category', 'model', 'unit'])
            ->latest()
            ->paginate($perPage);
    }

    public function findById(int $id): Warehousemodel
    {
        return Warehousemodel::with(['category', 'model', 'unit'])
            ->findOrFail($id);
    }

    public function delete(int $id): bool
    {
        return Warehousemodel::findOrFail($id)->delete();
    }
}
