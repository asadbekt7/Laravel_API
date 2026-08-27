<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\WarehouseItem;
use App\Models\WarehouseModel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class EloquentWarehouseRepository implements WarehouseRepositoryInterface
{
    // show() / PDF uchun TO'LIQ ma'lumot
    private const array DETAIL_RELATIONS = [
        'information.supplier',
        'information.creator',
        'location',
        'assignee',
        'items.informationItem.unit',
        'items.type',
        'items.category',
        'items.model',
    ];

    /**
     * Ro'yxat (jadval) uchun YENGIL so'rov - itemlarning o'zini emas,
     * faqat SONI (items_count) va YIG'INDISI (items_total) kerak, shuning
     * uchun ularni PHP darajasida emas, SQL darajasida hisoblaymiz.
     */
    public function paginate(array $filters, int $perPage): LengthAwarePaginator
    {
        return WarehouseModel::query()
            ->with(['information:id,contract_number,supplier_id', 'information.supplier:id,name'])
            ->withCount('items')
            ->addSelect([
                'items_total' => WarehouseItem::query()
                    ->selectRaw('COALESCE(SUM(information_items.total_price), 0)')
                    ->join('information_items', 'information_items.id', '=', 'warehouse_items.information_item_id')
                    ->whereColumn('warehouse_items.warehouse_id', 'warehouse.id'),
            ])
            ->when($filters['akt_number'] ?? null, function (Builder $q, string $search) {
                $q->where('akt_number', 'ilike', "%{$search}%");
            })
            ->when($filters['location_id'] ?? null, fn ($q, $id) => $q->where('location_id', $id))
            ->when($filters['information_id'] ?? null, fn ($q, $id) => $q->where('information_id', $id))
            ->when($filters['akt_date_from'] ?? null, fn ($q, $date) => $q->whereDate('akt_date', '>=', $date))
            ->when($filters['akt_date_to'] ?? null, fn ($q, $date) => $q->whereDate('akt_date', '<=', $date))
            ->latest()
            ->paginate($perPage);
    }

    public function findOrFailWithRelations(int $id): WarehouseModel
    {
        return WarehouseModel::with(self::DETAIL_RELATIONS)->findOrFail($id);
    }
}
