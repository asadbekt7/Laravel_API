<?php

namespace App\Http\Filters;

use App\Http\Filters\Concerns\Sortable;
use Illuminate\Database\Eloquent\Builder;

class WarehouseFilter extends QueryFilter
{
    use Sortable;

    protected array $allowedSorts = [
        'id',
        'name',
        'quantity',
        'product_price',
        'created_at',
    ];

    // ─── Aniq qidiruvlar ─────────────────────────────────────────────

    public function information_id(Builder $q, mixed $value): void
    {
        $q->where('information_id', $value);
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

    public function unit_id(Builder $q, mixed $value): void
    {
        $q->where('unit_id', $value);
    }

    public function location_id(Builder $q, mixed $value): void
    {
        $q->where('location_id', $value);
    }

    // ─── LIKE qidiruvlar ─────────────────────────────────────────────

    public function name(Builder $q, string $value): void
    {
        $q->where('name', 'like', "%{$value}%");
    }

    public function description(Builder $q, string $value): void
    {
        $q->where('description', 'like', "%{$value}%");
    }

    // ─── Narx oralig'i ───────────────────────────────────────────────

    public function price_from(Builder $q, mixed $value): void
    {
        $q->where('product_price', '>=', $value);
    }

    public function price_to(Builder $q, mixed $value): void
    {
        $q->where('product_price', '<=', $value);
    }

    // ─── Miqdor oralig'i ─────────────────────────────────────────────

    public function quantity_from(Builder $q, mixed $value): void
    {
        $q->where('quantity', '>=', $value);
    }

    public function quantity_to(Builder $q, mixed $value): void
    {
        $q->where('quantity', '<=', $value);
    }

    // ─── Sana oralig'i ───────────────────────────────────────────────

    public function created_from(Builder $q, string $value): void
    {
        $q->whereDate('created_at', '>=', $value);
    }

    public function created_to(Builder $q, string $value): void
    {
        $q->whereDate('created_at', '<=', $value);
    }
}
