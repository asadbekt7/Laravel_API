<?php

declare(strict_types=1);

namespace App\Http\Filters;

use App\Http\Filters\Concerns\Sortable;
use Illuminate\Database\Eloquent\Builder;

/**
 * warehouse_items uchun filter. Diqqat: warehouse_items jadvalining o'zida
 * faqat warehouse_id/type_id/category_id/model_id bor — product_name,
 * quantity, item_price kabi maydonlar information_items jadvalida, shuning
 * uchun ular whereHas('informationItem', ...) orqali filtrlanadi.
 */
class WarehouseItemFilter extends QueryFilter
{
    use Sortable;

    // warehouse_items jadvalida haqiqatan mavjud ustunlar bilan cheklandi —
    // aks holda orderBy() SQL xatosi beradi. informationItem ustunlari
    // (masalan quantity) bo'yicha saralash JOIN talab qiladi (pastda izoh).
    protected array $allowedSorts = [
        'id',
        'created_at',
    ];

    // ─── warehouse_items ustunlari bo'yicha aniq qidiruv ───────────────

    public function warehouse_id(Builder $q, mixed $value): void
    {
        $q->where('warehouse_id', $value);
    }

    public function type_id(Builder $q, mixed $value): void
    {
        $q->where('type_id', $value);
    }

    public function category_id(Builder $q, mixed $value): void
    {
        $q->where('category_id', $value);
    }

    public function model_id(Builder $q, mixed $value): void
    {
        $q->where('model_id', $value);
    }

    // ─── information_items orqali (relatsiya) qidiruvlar ────────────────

    public function unit_id(Builder $q, mixed $value): void
    {
        $q->whereHas('informationItem', fn (Builder $iq) => $iq->where('unit_id', $value));
    }

    public function product_name(Builder $q, string $value): void
    {
        // PostgreSQL: case-insensitive qidiruv uchun LIKE emas, ILIKE.
        $q->whereHas(
            'informationItem',
            fn (Builder $iq) => $iq->where('product_name', 'ilike', "%{$value}%")
        );
    }

    // ─── Narx oralig'i (information_items.item_price) ────────────────────

    public function price_from(Builder $q, mixed $value): void
    {
        $q->whereHas('informationItem', fn (Builder $iq) => $iq->where('item_price', '>=', $value));
    }

    public function price_to(Builder $q, mixed $value): void
    {
        $q->whereHas('informationItem', fn (Builder $iq) => $iq->where('item_price', '<=', $value));
    }

    // ─── Miqdor oralig'i (information_items.quantity) ──────────────────

    public function quantity_from(Builder $q, mixed $value): void
    {
        $q->whereHas('informationItem', fn (Builder $iq) => $iq->where('quantity', '>=', $value));
    }

    public function quantity_to(Builder $q, mixed $value): void
    {
        $q->whereHas('informationItem', fn (Builder $iq) => $iq->where('quantity', '<=', $value));
    }

    // ─── Sana oralig'i (warehouse_items.created_at) ─────────────────────

    public function created_from(Builder $q, string $value): void
    {
        $q->whereDate('created_at', '>=', $value);
    }

    public function created_to(Builder $q, string $value): void
    {
        $q->whereDate('created_at', '<=', $value);
    }

    // ─── Akt raqami bo'yicha (warehouse.akt_number) ─────────────────────

    public function akt_number(Builder $q, string $value): void
    {
        $q->whereHas('warehouse', fn (Builder $wq) => $wq->whereRaw('akt_number::text ILIKE ?', ["%{$value}%"]));
    }
}
