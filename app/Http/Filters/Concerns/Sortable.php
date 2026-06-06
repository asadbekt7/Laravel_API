<?php

namespace App\Http\Filters\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait Sortable
{
    // har bir filter klassida o'zi e'lon qiladi
    // protected array $allowedSorts = [...];

    public function applySorting(Builder $query, Request $request): void
    {
        $sortField     = $request->input('sort_by', 'created_at');
        $sortDirection = $request->input('sort_dir', 'desc') === 'asc' ? 'asc' : 'desc';

        if (in_array($sortField, $this->allowedSorts, true)) {
            $query->orderBy($sortField, $sortDirection);
        }
    }

    public function getPerPage(Request $request): int
    {
        return min(max((int) $request->input('per_page', 15), 1), 100);
    }
}
