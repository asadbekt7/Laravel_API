<?php

declare(strict_types=1);

namespace App\Services;

use App\Actions\Warehouse\AcceptInformationToWarehouseAction;
use App\Actions\Warehouse\RejectInformationAction;
use App\DTOs\Warehouse\WarehouseAcceptData;
use App\Models\InformationModel;
use App\Models\WarehouseModel;
use Illuminate\Support\Facades\DB;

final readonly class WarehouseAcceptanceService
{
    public function __construct(
        private AcceptInformationToWarehouseAction $acceptAction,
        private RejectInformationAction $rejectAction,
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
}
