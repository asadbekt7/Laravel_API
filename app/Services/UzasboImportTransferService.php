<?php

namespace App\Services;

use App\Exceptions\RoomApiException;
use App\Http\Requests\TransferUzasboImportRequest;
use App\Models\ItemsModel;
use App\Models\UnitModel;
use App\Models\UzasboImportModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

readonly class UzasboImportTransferService
{
    public function __construct(
        private StaffApiService $staffApiService,
        private RoomApiService  $roomApiService,
    ) {}

    public function transfer(TransferUzasboImportRequest $request): array
    {
        $importIds  = $request->input('import_ids');
        $typeId     = $request->input('type_id');
        $categoryId = $request->input('category_id');
        $modelId    = $request->input('model_id');
        $roomName   = $request->input('room_name');
        $staffId    = (int) $request->input('staff_id');

        // 1. Staff mavjudligini tekshirish
        $this->validateStaffExists($staffId);

        // 2. Room ma'lumotlarini tashqi API dan olish
        // getRoomData() topilmasa RoomApiException tashlaydi
        // topilsa: ['room_name' => ..., 'room_number' => ..., 'building' => ...]
        $roomData = $this->roomApiService->getRoomData($roomName);

        $transferred = [];
        $skipped     = [];

        try {
            DB::transaction(function () use (
                $importIds,
                $typeId,
                $categoryId,
                $modelId,
                $roomData,
                $staffId,
                &$transferred,
                &$skipped
            ) {
                $imports = UzasboImportModel::whereIn('id', $importIds)
                    ->where(function ($query) {
                        $query->whereNull('status')
                            ->orWhere('status', '!=', 'transferred');
                    })
                    ->get();

                $fetchedIds     = $imports->pluck('id')->toArray();
                $alreadyDoneIds = array_diff($importIds, $fetchedIds);

                foreach ($alreadyDoneIds as $doneId) {
                    $skipped[] = [
                        'import_id'        => $doneId,
                        'inventory_number' => null,
                        'reason'           => 'Already transferred',
                    ];
                }

                foreach ($imports as $import) {
                    $exists = ItemsModel::where('inventory_number', $import->inventory_number)->exists();

                    if ($exists) {
                        $skipped[] = [
                            'import_id'        => $import->id,
                            'inventory_number' => $import->inventory_number,
                            'reason'           => 'inventory_number already exists in items table',
                        ];
                        continue;
                    }

                    $unitId = $this->resolveUnitId($import->unit);

                    ItemsModel::create([
                        'uzasbo_import_id' => $import->id,
                        'name'             => $import->name,
                        'type_id'          => $typeId,
                        'category_id'      => $categoryId,
                        'model_id'         => $modelId,
                        'quantity'         => 1,
                        'unit_id'          => $unitId,
                        'inventory_number' => $import->inventory_number,
                        'room_name'        => $roomData['room_name'],
                        'building'         => $roomData['building'],
                        'room_number'      => $roomData['room_number'],
                        'staff_id'         => $staffId,
                        'condition'        => 'new',
                        'status'           => 'active',
                        'notes'            => null,
                    ]);

                    $import->status = 'transferred';
                    $import->save();

                    $transferred[] = $import->id;
                }
            });

        } catch (Throwable $e) {
            Log::error('UzasboImportTransferService::transfer xatosi', [
                'message'    => $e->getMessage(),
                'import_ids' => $importIds,
                'trace'      => $e->getTraceAsString(),
            ]);

            throw $e;
        }

        return [
            'transferred' => $transferred,
            'skipped'     => $skipped,
            'errors'      => [],
        ];
    }

    private function validateStaffExists(int $staffId): void
    {
        // getStaffById() bo'sh array qaytarsa — topilmadi
        $staff = $this->staffApiService->getStaffById($staffId);

        if (empty($staff)) {
            throw new \InvalidArgumentException(
                "staff_id {$staffId} tashqi Staff API da topilmadi."
            );
        }
    }

    private function resolveUnitId(?string $unitName): int
    {
        $defaultUnitId = (int) config('app.default_unit_id', env('DEFAULT_UNIT_ID', 1));

        if (empty($unitName)) {
            return $defaultUnitId;
        }

        $unit = UnitModel::where('name', $unitName)->first();

        return $unit ? $unit->id : $defaultUnitId;
    }
}
