<?php

declare(strict_types=1);

namespace App\Repositories\Receiving;

use App\Models\InformationModel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * information jadvali ustidagi akt_number bo'yicha guruhlash mantig'ini
 * Service qatlamidan ajratib turadi — shu orqali murakkab agregat so'rovlar
 * bitta joyda jamlanadi va kelajakda keshlash/optimallashtirish osonlashadi.
 */
final class ReceivingRepository
{
    /**
     * /receiving — akt_number bo'yicha guruhlangan ro'yxat (har bir akt uchun bitta qator).
     */
    public function paginateGrouped(
        ?string $search,
        ?int $supplierId,
        ?string $fromDate,
        ?string $toDate,
        int $perPage,
    ): LengthAwarePaginator {
        return InformationModel::query()
            ->select([
                'akt_number',
                DB::raw('MIN(akt_date) as akt_date'),
                DB::raw('MIN(contract_number) as contract_number'),
                DB::raw('MIN(supplier_id) as supplier_id'),
                DB::raw('COUNT(*) as items_count'),
                DB::raw('SUM(quantity * price) as total_sum'),
            ])
            ->completed()
            ->when(
                $search,
                fn ($query, string $value) => $query->where(function ($query) use ($value) {
                    $query->where('akt_number', 'ilike', "%{$value}%")
                        ->orWhere('contract_number', 'ilike', "%{$value}%");
                })
            )
            ->when($supplierId, fn ($query, int $id) => $query->where('supplier_id', $id))
            ->when($fromDate, fn ($query, string $date) => $query->whereDate('akt_date', '>=', $date))
            ->when($toDate, fn ($query, string $date) => $query->whereDate('akt_date', '<=', $date))
            ->groupBy('akt_number')
            ->orderByDesc('akt_date')
            ->with('supplier:id,name')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Bitta akt_number'ga tegishli BARCHA mahsulot qatorlarini (yakunlangan holatda) qaytaradi.
     *
     * @return Collection<int, InformationModel>
     */
    public function findCompletedByAktNumber(string $aktNumber): Collection
    {
        return InformationModel::query()
            ->with(['supplier:id,name', 'unit:id,name', 'assignee:id,name'])
            ->completed()
            ->where('akt_number', $aktNumber)
            ->orderBy('id')
            ->get();
    }
}
