<?php

namespace App\Http\Filters;

use App\Http\Filters\Concerns\Sortable;
use Illuminate\Database\Eloquent\Builder;

class InformationFilter extends QueryFilter
{
    use Sortable;
    protected array $allowedSorts = [
        'id',
        'akt_raqam',
        'akt_sana',
        'shartnoma_raqam',
        'shartnoma_sana',
        'ombor_qabul_sana',
        'jami_summa',
        'created_at',
    ];
    public function supplier_id(Builder $q, mixed $value): void
    {
        $q->where('supplier_id', $value);
    }

    public function location_id(Builder $q, mixed $value): void
    {
        $q->where('location_id', $value);
    }

    public function xaridor_full_name(Builder $q, string $value): void
    {
        $q->where('xaridor_full_name', 'like', "%{$value}%");
    }

    public function akt_raqam(Builder $q, string $value): void
    {
        $q->where('akt_raqam', 'like', "%{$value}%");
    }

    public function shartnoma_raqam(Builder $q, string $value): void
    {
        $q->where('shartnoma_raqam', 'like', "%{$value}%");
    }

    public function ishonchnoma_raqam(Builder $q, string $value): void
    {
        $q->where('ishonchnoma_raqam', 'like', "%{$value}%");
    }

    public function schet_faktura_raqam(Builder $q, string $value): void
    {
        $q->where('schet_faktura_raqam', 'like', "%{$value}%");
    }

    public function ombor_qabul_xodim_full_name(Builder $q, string $value): void
    {
        $q->where('ombor_qabul_xodim_full_name', 'like', "%{$value}%");
    }

    public function akt_sana_from(Builder $q, string $value): void
    {
        $q->whereDate('akt_sana', '>=', $value);
    }

    public function akt_sana_to(Builder $q, string $value): void
    {
        $q->whereDate('akt_sana', '<=', $value);
    }

    public function shartnoma_sana_from(Builder $q, string $value): void
    {
        $q->whereDate('shartnoma_sana', '>=', $value);
    }

    public function shartnoma_sana_to(Builder $q, string $value): void
    {
        $q->whereDate('shartnoma_sana', '<=', $value);
    }

    public function ombor_qabul_sana_from(Builder $q, string $value): void
    {
        $q->whereDate('ombor_qabul_sana', '>=', $value);
    }

    public function ombor_qabul_sana_to(Builder $q, string $value): void
    {
        $q->whereDate('ombor_qabul_sana', '<=', $value);
    }

    public function schet_faktura_sana_from(Builder $q, string $value): void
    {
        $q->whereDate('schet_faktura_sana', '>=', $value);
    }

    public function schet_faktura_sana_to(Builder $q, string $value): void
    {
        $q->whereDate('schet_faktura_sana', '<=', $value);
    }

    public function ishonchnoma_sana_from(Builder $q, string $value): void
    {
        $q->whereDate('ishonchnoma_sana', '>=', $value);
    }

    public function ishonchnoma_sana_to(Builder $q, string $value): void
    {
        $q->whereDate('ishonchnoma_sana', '<=', $value);
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
