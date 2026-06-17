<?php

namespace App\Http\Filters;

use App\Http\Filters\Concerns\Sortable;
use Illuminate\Database\Eloquent\Builder;

class MalumotFilter extends QueryFilter
{
    use Sortable;

    protected array $allowedSorts = [
        'id',
        'contract_number',
        'contract_date',
        'bildirishnoma_number',
        'bildirishnoma_date',
        'ishonchnoma_date',
        'hisob_faktura_date',
        'akt_date',
        'name',
        'quantity',
        'price',
        'created_at',
    ];

    // ─── Filter metodlari ────────────────────────────────────────────────────────

    /** ?contract_number=123 */
    public function contract_number(Builder $query, string $value): void
    {
        $query->where('contract_number', 'like', "%{$value}%");
    }

    /** ?contract_date_from=2024-01-01 */
    public function contract_date_from(Builder $query, string $value): void
    {
        $query->whereDate('contract_date', '>=', $value);
    }

    /** ?contract_date_to=2024-12-31 */
    public function contract_date_to(Builder $query, string $value): void
    {
        $query->whereDate('contract_date', '<=', $value);
    }

    /** ?yetkazuvchi_id=5 */
    public function yetkazuvchi_id(Builder $query, string $value): void
    {
        $query->where('yetkazuvchi_id', $value);
    }

    /** ?bildirishnoma_number=456 */
    public function bildirishnoma_number(Builder $query, string $value): void
    {
        $query->where('bildirishnoma_number', 'like', "%{$value}%");
    }

    /** ?bildirishnoma_date_from=2024-01-01 */
    public function bildirishnoma_date_from(Builder $query, string $value): void
    {
        $query->whereDate('bildirishnoma_date', '>=', $value);
    }

    /** ?bildirishnoma_date_to=2024-12-31 */
    public function bildirishnoma_date_to(Builder $query, string $value): void
    {
        $query->whereDate('bildirishnoma_date', '<=', $value);
    }

    /** ?name=mahsulot */
    public function name(Builder $query, string $value): void
    {
        $query->where('name', 'like', "%{$value}%");
    }

    /** ?unit_id=2 */
    public function unit_id(Builder $query, string $value): void
    {
        $query->where('unit_id', $value);
    }

    /** ?price_min=1000 */
    public function price_min(Builder $query, string $value): void
    {
        $query->where('price', '>=', $value);
    }

    /** ?price_max=50000 */
    public function price_max(Builder $query, string $value): void
    {
        $query->where('price', '<=', $value);
    }

    /** ?quantity_min=10 */
    public function quantity_min(Builder $query, string $value): void
    {
        $query->where('quantity', '>=', $value);
    }

    /** ?quantity_max=100 */
    public function quantity_max(Builder $query, string $value): void
    {
        $query->where('quantity', '<=', $value);
    }

    /** ?ishonchnoma_number=789 */
    public function ishonchnoma_number(Builder $query, string $value): void
    {
        $query->where('ishonchnoma_number', 'like', "%{$value}%");
    }

    /** ?ishonchnoma_date_from=2024-01-01 */
    public function ishonchnoma_date_from(Builder $query, string $value): void
    {
        $query->whereDate('ishonchnoma_date', '>=', $value);
    }

    /** ?ishonchnoma_date_to=2024-12-31 */
    public function ishonchnoma_date_to(Builder $query, string $value): void
    {
        $query->whereDate('ishonchnoma_date', '<=', $value);
    }

    /** ?hisob_faktura=HF-001 */
    public function hisob_faktura(Builder $query, string $value): void
    {
        $query->where('hisob_faktura', 'like', "%{$value}%");
    }

    /** ?hisob_faktura_date_from=2024-01-01 */
    public function hisob_faktura_date_from(Builder $query, string $value): void
    {
        $query->whereDate('hisob_faktura_date', '>=', $value);
    }

    /** ?hisob_faktura_date_to=2024-12-31 */
    public function hisob_faktura_date_to(Builder $query, string $value): void
    {
        $query->whereDate('hisob_faktura_date', '<=', $value);
    }

    /** ?akt_number=AKT-001 */
    public function akt_number(Builder $query, string $value): void
    {
        $query->where('akt_number', 'like', "%{$value}%");
    }

    /** ?akt_date_from=2024-01-01 */
    public function akt_date_from(Builder $query, string $value): void
    {
        $query->whereDate('akt_date', '>=', $value);
    }

    /** ?akt_date_to=2024-12-31 */
    public function akt_date_to(Builder $query, string $value): void
    {
        $query->whereDate('akt_date', '<=', $value);
    }

    /** ?search=qidiruv — barcha asosiy matn maydonlardan qidirish */
    public function search(Builder $query, string $value): void
    {
        $query->where(function (Builder $q) use ($value) {
            $q->where('contract_number',      'like', "%{$value}%")
                ->orWhere('bildirishnoma_number','like', "%{$value}%")
                ->orWhere('ishonchnoma_number',  'like', "%{$value}%")
                ->orWhere('hisob_faktura',       'like', "%{$value}%")
                ->orWhere('akt_number',          'like', "%{$value}%")
                ->orWhere('name',                'like', "%{$value}%")
                ->orWhere('description',         'like', "%{$value}%");
        });
    }
}
