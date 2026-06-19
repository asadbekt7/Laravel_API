<?php

namespace App\Http\Filters;

use App\Http\Filters\Concerns\Sortable;
use Illuminate\Database\Eloquent\Builder;

class InformationFilter extends QueryFilter
{
    use Sortable;

    protected array $allowedSorts = [
        'id',
        'name',
        'contract_number',
        'contract_date',
        'bildirishnoma_number',
        'bildirishnoma_date',
        'ishonchnoma_number',
        'ishonchnoma_date',
        'hisob_faktura',
        'hisob_faktura_date',
        'akt_number',
        'akt_date',
        'quantity',
        'price',
        'created_at',
    ];

    public function supplier_id(Builder $q, mixed $value): void
    {
        $q->where('supplier_id', $value);
    }

    public function unit_id(Builder $q, mixed $value): void
    {
        $q->where('unit_id', $value);
    }

    public function name(Builder $q, string $value): void
    {
        $q->where('name', 'like', "%{$value}%");
    }

    public function product_name(Builder $q, string $value): void
    {
        $q->where('product_name', 'like', "%{$value}%");
    }

    public function contract_number(Builder $q, string $value): void
    {
        $q->where('contract_number', 'like', "%{$value}%");
    }

    public function bildirishnoma_number(Builder $q, string $value): void
    {
        $q->where('bildirishnoma_number', 'like', "%{$value}%");
    }

    public function ishonchnoma_number(Builder $q, string $value): void
    {
        $q->where('ishonchnoma_number', 'like', "%{$value}%");
    }

    public function hisob_faktura(Builder $q, string $value): void
    {
        $q->where('hisob_faktura', 'like', "%{$value}%");
    }

    public function akt_number(Builder $q, string $value): void
    {
        $q->where('akt_number', 'like', "%{$value}%");
    }

    // contract_date range
    public function contract_date_from(Builder $q, string $value): void
    {
        $q->whereDate('contract_date', '>=', $value);
    }

    public function contract_date_to(Builder $q, string $value): void
    {
        $q->whereDate('contract_date', '<=', $value);
    }

    // bildirishnoma_date range
    public function bildirishnoma_date_from(Builder $q, string $value): void
    {
        $q->whereDate('bildirishnoma_date', '>=', $value);
    }

    public function bildirishnoma_date_to(Builder $q, string $value): void
    {
        $q->whereDate('bildirishnoma_date', '<=', $value);
    }

    // ishonchnoma_date range
    public function ishonchnoma_date_from(Builder $q, string $value): void
    {
        $q->whereDate('ishonchnoma_date', '>=', $value);
    }

    public function ishonchnoma_date_to(Builder $q, string $value): void
    {
        $q->whereDate('ishonchnoma_date', '<=', $value);
    }

    // hisob_faktura_date range
    public function hisob_faktura_date_from(Builder $q, string $value): void
    {
        $q->whereDate('hisob_faktura_date', '>=', $value);
    }

    public function hisob_faktura_date_to(Builder $q, string $value): void
    {
        $q->whereDate('hisob_faktura_date', '<=', $value);
    }

    // akt_date range
    public function akt_date_from(Builder $q, string $value): void
    {
        $q->whereDate('akt_date', '>=', $value);
    }

    public function akt_date_to(Builder $q, string $value): void
    {
        $q->whereDate('akt_date', '<=', $value);
    }

    // price range
    public function price_from(Builder $q, mixed $value): void
    {
        $q->where('price', '>=', $value);
    }

    public function price_to(Builder $q, mixed $value): void
    {
        $q->where('price', '<=', $value);
    }

    // quantity range
    public function quantity_from(Builder $q, mixed $value): void
    {
        $q->where('quantity', '>=', $value);
    }

    public function quantity_to(Builder $q, mixed $value): void
    {
        $q->where('quantity', '<=', $value);
    }

    public function with_trashed(Builder $q, mixed $value): void
    {
        if (filter_var($value, FILTER_VALIDATE_BOOLEAN)) {
            $q->withTrashed();
        }
    }

    public function only_trashed(Builder $q, mixed $value): void
    {
        if (filter_var($value, FILTER_VALIDATE_BOOLEAN)) {
            $q->onlyTrashed();
        }
    }
}
