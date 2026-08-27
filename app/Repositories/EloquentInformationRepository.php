<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\InformationStatus;
use App\Models\InformationModel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class EloquentInformationRepository implements InformationRepositoryInterface
{
    private const array RELATIONS = ['supplier', 'creator', 'items.unit'];

    public function paginate(array $filters, int $perPage): LengthAwarePaginator
    {
        return InformationModel::query()
            ->with(self::RELATIONS)
            ->when($filters['search'] ?? null, function (Builder $q, string $search) {
                $q->where(function (Builder $q) use ($search) {
                    $q->where('name', 'ilike', "%{$search}%")
                        ->orWhere('contract_number', 'ilike', "%{$search}%");
                });
            })
            ->when($filters['supplier_id'] ?? null, fn ($q, $id) => $q->where('supplier_id', $id))
            ->when($filters['from_date'] ?? null, fn ($q, $date) => $q->whereDate('contract_date', '>=', $date))
            ->when($filters['to_date'] ?? null, fn ($q, $date) => $q->whereDate('contract_date', '<=', $date))
            ->when($filters['status'] ?? null, function ($q, $status) {
                if ($enum = InformationStatus::tryFrom($status)) {
                    $q->where('status', $enum);
                }
            })
            ->when($filters['created_by_me'] ?? null, fn ($q) => $q->where('creator_id', auth()->id()))
            ->latest()
            ->paginate($perPage);
    }

    public function findOrFailWithRelations(int $id): InformationModel
    {
        return InformationModel::with(self::RELATIONS)->findOrFail($id);
    }
}
