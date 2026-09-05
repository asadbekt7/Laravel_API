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
            // YANGI: 'tmz' relatsiyasi qo'shildi — shunda WarehouseItemResource
            // 'tmz' maydonini N+1 so'rovsiz to'liq obyekt sifatida chiqara oladi.
            'tmz',
        ]);

        $filter->apply($query);
        $filter->applySorting($query, $filter->request);

        return $query->paginate($perPage)->withQueryString();
    }
}
