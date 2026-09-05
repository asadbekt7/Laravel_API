<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\InformationItem;
use App\Models\WarehouseModel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

/**
 * "/receiving" (qabul qilingan aktlar) uchun FAQAT O'QISH mantig'i.
 *
 * DIQQAT: bu klass yozish (accept/reject) bilan shug'ullanmaydi — u allaqachon
 * WarehouseAcceptanceService + AcceptInformationToWarehouseAction'da bor.
 * CQS (Command-Query Separation) saqlanishi uchun o'qish alohida klassda.
 */
final class WarehouseService
{
    /**
     * Aktlar ro'yxati (WarehouseListResource uchun): items_count va items_total
     * agregat subquery orqali hisoblanadi — items relatsiyasi TO'LIQ yuklanmaydi,
     * shuning uchun 1000 ta akt bo'lsa ham bitta SQL so'rov.
     */
    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return WarehouseModel::query()
            // WarehouseListResource ichida $this->information?->supplier?->name ishlatiladi —
            // shuning uchun N+1'ning oldini olish uchun aynan shu ikki bosqich eager-load qilinadi.
            ->with('information.supplier')
            ->withCount('items')
            ->addSelect([
                'items_total' => InformationItem::query()
                    ->selectRaw('COALESCE(SUM(information_items.total_price), 0)')
                    ->join('warehouse_items', 'warehouse_items.information_item_id', '=', 'information_items.id')
                    ->whereColumn('warehouse_items.warehouse_id', 'warehouse.id'),
            ])
            ->withCasts(['items_total' => 'decimal:2'])
            ->when(
                $filters['akt_number'] ?? null,
                // PostgreSQL: case-insensitive qidiruv uchun LIKE emas, ILIKE.
                fn (Builder $q, string $v) => $q->whereRaw('akt_number::text ILIKE ?', ["%{$v}%"])
            )
            ->when($filters['akt_date_from'] ?? null, fn (Builder $q, string $v) => $q->whereDate('akt_date', '>=', $v))
            ->when($filters['akt_date_to'] ?? null, fn (Builder $q, string $v) => $q->whereDate('akt_date', '<=', $v))
            ->when($filters['status'] ?? null, fn (Builder $q, string $v) => $q->where('status', $v))
            ->when($filters['location_id'] ?? null, fn (Builder $q, int $v) => $q->where('location_id', $v))
            ->orderByDesc('akt_date')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Bitta aktning to'liq tafsiloti (WarehouseResource uchun).
     *
     * $warehouse allaqachon route-model-binding orqali yuklangan — load() faqat
     * relatsiyalarni oladi, bazaviy qatorni qayta so'ramaydi (qo'shimcha so'rov yo'q).
     * Eager-load ro'yxati AcceptInformationToWarehouseAction'dagi bilan bir xil —
     * arxitektura izchilligi uchun.
     */
    public function detail(WarehouseModel $warehouse): WarehouseModel
    {
        return $warehouse->load([
            'items.informationItem.unit',
            'items.type',
            'items.category',
            'items.model',
            // YANGI: 'items.tmz' qo'shildi — GET /api/receiving/{warehouse} javobida
            // TMZ turidagi itemlar uchun ham to'liq ma'lumot (N+1'siz) chiqishi uchun.
            'items.tmz',
            'location',
            'assignee',
            'information',
            'information.creator',
            'information.supplier',
        ]);
    }
}
