<?php

namespace App\Services;

use App\Exceptions\ExternalApi\ApiConnectionException;
use App\Exceptions\ExternalApi\ApiInvalidResponseException;
use App\Exceptions\ExternalApi\ApiRequestFailedException;
use App\Exceptions\RoomApiException;
use App\Models\ItemsModel;
use App\Models\UzasboImportModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UzasboImportTransferService
{
    public function __construct(
        protected RoomApiService  $roomApiService,
        protected StaffApiService $staffApiService,
    ) {}
    public function transfer(array $importIds, array $params): array
    {
        $importIds = array_values(array_unique($importIds));

        // Tashqi API lar — tranzaksiya tashqarisida
        $roomInfo  = $this->fetchRoomInfo($params['room_name']);
        $staffInfo = $this->fetchStaffInfo($params);
        $fullName  = $this->buildFullName($staffInfo);
        $unitMap   = $this->loadUnitMap();

        $transferred = [];
        $skipped     = [];
        $errors      = [];

        // Asosiy tranzaksiya — lock olish uchun
        DB::transaction(function () use (
            $importIds, $params, $roomInfo, $staffInfo, $fullName, $unitMap,
            &$transferred, &$skipped, &$errors
        ) {
            // lockForUpdate tranzaksiya ICHIDA
            $imports = UzasboImportModel::whereIn('id', $importIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($importIds as $importId) {
                /** @var UzasboImportModel|null $import */
                $import = $imports->get($importId);

                if (! $import) {
                    $skipped[] = ['id' => $importId, 'reason' => 'Yozuv topilmadi.'];
                    continue;
                }

                if ($import->status === 'transfered') {
                    $skipped[] = [
                        'id'               => $importId,
                        'inventory_number' => $import->inventory_number,
                        'reason'           => 'Allaqachon transfer qilingan.',
                    ];
                    continue;
                }

                // Har bir yozuv uchun nested transaction (PostgreSQL savepoint yaratadi)
                try {
                    DB::transaction(function () use (
                        $import, $params, $roomInfo, $staffInfo, $fullName, $unitMap,
                        &$transferred
                    ) {
                        $unitId   = $this->resolveUnitId($import->unit, $unitMap);
                        $quantity = max(1, (int) ($import->closing_quantity ?? 1));

                        $item = ItemsModel::create([
                            'uzasbo_import_id' => $import->id,
                            'receiving_id'     => null,
                            'name'             => $import->name,
                            'document_number'  => $import->account_number,
                            'supplier_name'    => null,
                            'batch_number'     => null,
                            'type_id'          => $params['type_id'],
                            'category_id'      => $params['category_id'],
                            'model_id'         => $params['model_id'],
                            'quantity'         => $quantity,
                            'unit_id'          => $unitId,
                            'inventory_number' => $import->inventory_number,
                            'room_name'        => $roomInfo['room_name'],
                            'building'         => $roomInfo['building'],
                            'room_number'      => $roomInfo['room_number'],
                            'last_name'        => $staffInfo['last_name'],
                            'first_name'       => $staffInfo['first_name'],
                            'middle_name'      => $staffInfo['middle_name'],
                            'full_name'        => $fullName,
                            'department'       => $staffInfo['department'] ?? $import->department,
                            'condition'        => 'good',
                            'status'           => 'active',
                            'notes'            => $import->expense_article,
                        ]);

                        $import->update(['status' => 'transfered']);

                        $transferred[] = [
                            'import_id'        => $import->id,
                            'item_id'          => $item->id,
                            'inventory_number' => $item->inventory_number,
                        ];
                    });

                } catch (\Exception $e) {
                    // Faqat shu yozuv rollback bo'ladi, qolganlar davom etadi
                    Log::error('UzasboImportTransferService: yozuv transfer qilinmadi.', [
                        'import_id' => $importId,
                        'error'     => $e->getMessage(),
                    ]);

                    $errors[] = [
                        'id'    => $importId,
                        'error' => $e->getMessage(),
                    ];
                }
            }

            if (empty($transferred) && ! empty($errors)) {
                throw new \RuntimeException(
                    'Barcha yozuvlarda xatolik yuz berdi. Transfer bekor qilindi.'
                );
            }
        });

        return [
            'transferred' => $transferred,
            'skipped'     => $skipped,
            'errors'      => $errors,
            'summary'     => [
                'total'       => count($importIds),
                'transferred' => count($transferred),
                'skipped'     => count($skipped),
                'errors'      => count($errors),
            ],
        ];
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /** @throws RoomApiException */
    private function fetchRoomInfo(string $roomName): array
    {
        return $this->roomApiService->getRoomData($roomName);
    }

    /**
     * @throws ApiConnectionException
     * @throws ApiRequestFailedException
     * @throws ApiInvalidResponseException
     * @throws \RuntimeException
     */
    private function fetchStaffInfo(array $params): array
    {
        $lastName = $params['last_name'];
        $results = $this->staffApiService->searchStaff($lastName);

        if (empty($results)) {
            throw new \RuntimeException("'{$lastName}' familiyali xodim topilmadi.");
        }

        if (!empty($params['staff_id'])) {
            $needle = (string) $params['staff_id'];
            foreach ($results as $s) {
                if ((string) ($s['id'] ?? '') === $needle) {
                    return $s;
                }
            }
            throw new \RuntimeException("ID={$needle} li xodim qidiruv natijalarida topilmadi.");
        }

        $hasFirst  = !empty($params['first_name']);
        $hasMiddle = !empty($params['middle_name']);
        if ($hasFirst || $hasMiddle) {
            foreach ($results as $s) {
                $firstOk  = !$hasFirst  || mb_strtolower(trim($s['first_name']  ?? '')) === mb_strtolower(trim($params['first_name']));
                $middleOk = !$hasMiddle || mb_strtolower(trim($s['middle_name'] ?? '')) === mb_strtolower(trim($params['middle_name']));
                if ($firstOk && $middleOk) {
                    return $s;
                }
            }
        }

        return $results[0];
    }

    /**
     * full_name ni ishonchli tarzda yasash.
     * API bo'sh string yoki null qaytarsa ham to'g'ri ishlaydi.
     */
    private function buildFullName(array $staffInfo): string
    {
        if (! empty($staffInfo['full_name'])) {
            return $staffInfo['full_name'];
        }

        return trim(implode(' ', array_filter([
            $staffInfo['last_name']   ?? '',
            $staffInfo['first_name']  ?? '',
            $staffInfo['middle_name'] ?? '',
        ])));
    }

    /**
     * Barcha unitlarni bir marta yuklab olish.
     *
     * @return array<string, int>
     */
    private function loadUnitMap(): array
    {
        return DB::table('units')
            ->get(['id', 'name'])
            ->mapWithKeys(fn($unit) => [mb_strtolower(trim($unit->name)) => $unit->id])
            ->toArray();
    }

    /**
     * @param  array<string, int>  $unitMap
     */
    private function resolveUnitId(?string $unitName, array $unitMap): int
    {
        if (! $unitName) {
            return 1;
        }

        return $unitMap[mb_strtolower(trim($unitName))] ?? 1;
    }
}
