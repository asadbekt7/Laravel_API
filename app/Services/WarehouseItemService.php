<?php

declare(strict_types=1);

namespace App\Services;

use App\Http\Filters\WarehouseItemFilter;
use App\Models\WarehouseItem;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class WarehouseItemService
{
    public function paginate(WarehouseItemFilter $filter, int $perPage): LengthAwarePaginator
    {
        $query = WarehouseItem::query()->with([
            'informationItem.unit',
            'type',
            'category',
            'model',
            'warehouse.location',
        ]);

        $filter->apply($query);
        $filter->applySorting($query, $filter->request);

        return $query->paginate($perPage)->withQueryString();
    }
}
