<?php

declare(strict_types=1);

namespace App\Services;

use App\Actions\Warehouse\AcceptInformationToWarehouseAction;
use App\Actions\Warehouse\RejectInformationAction;
use App\Actions\Warehouse\UpdateWarehouseAktAction;
use App\DTOs\Warehouse\WarehouseAcceptData;
use App\DTOs\Warehouse\WarehouseUpdateData;
use App\Models\InformationModel;
use App\Models\WarehouseModel;
use Illuminate\Support\Facades\DB;

final readonly class WarehouseAcceptanceService
{
    public function __construct(
        private AcceptInformationToWarehouseAction $acceptAction,
        private RejectInformationAction $rejectAction,
        private UpdateWarehouseAktAction $updateAction,
    ) {
    }

    public function accept(InformationModel $information, WarehouseAcceptData $data): WarehouseModel
    {
        return DB::transaction(fn () => $this->acceptAction->execute($information, $data));
    }

    public function reject(InformationModel $information, string $reason): bool
    {
        return DB::transaction(fn () => $this->rejectAction->execute($information, $reason));
    }

    /**
     * Allaqachon yaratilgan aktning akt_number/akt_date'ini tahrirlash.
     */
    public function updateAkt(WarehouseModel $warehouse, WarehouseUpdateData $data): WarehouseModel
    {
        return DB::transaction(fn () => $this->updateAction->execute($warehouse, $data));
    }
}
