<?php

namespace App\Services\ItemService;

use App\Exceptions\ExternalApi\ApiConnectionException;
use App\Exceptions\ExternalApi\ApiInvalidResponseException;
use App\Exceptions\ExternalApi\ApiRequestFailedException;
use App\Exceptions\RoomApiException;
use App\Models\ItemsModel;
use App\Services\RoomApiService;
use App\Services\StaffApiService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EditItemStaffService
{
    public function __construct(
        protected RoomApiService  $roomApiService,
        protected StaffApiService $staffApiService,
    ) {}

    /**
     * Bir yoki bir nechta item uchun xona va/yoki xodim ma'lumotlarini yangilaydi.
     *
     * $params misoli:
     * [
     *     // Xona (ixtiyoriy — faqat o'zgartirmoqchi bo'lsa)
     *     'room_name'   => 'A-101',
     *
     *     // Xodim (ixtiyoriy — faqat o'zgartirmoqchi bo'lsa)
     *     'last_name'   => 'Karimov',
     *     'first_name'  => 'Jasur',      // (ixtiyoriy, aniqlashtirish uchun)
     *     'middle_name' => 'Aliyevich',  // (ixtiyoriy, aniqlashtirish uchun)
     *     'staff_id'    => 42,           // (ixtiyoriy, eng aniq usul)
     * ]
     *
     * @param  int[]  $itemIds
     * @param  array  $params
     * @return array{ updated: array, skipped: array, errors: array, summary: array }
     *
     * @throws RoomApiException
     * @throws ApiConnectionException
     * @throws ApiRequestFailedException
     * @throws ApiInvalidResponseException
     * @throws \InvalidArgumentException
     */
    public function edit(array $itemIds, array $params): array
    {
        $itemIds = array_values(array_unique($itemIds));

        $this->validateParams($params);

        // Tashqi API chaqiruvlari — tranzaksiya tashqarisida (DB lock olmasdan)
        $roomInfo  = $this->resolveRoomInfo($params);
        $staffInfo = $this->resolveStaffInfo($params);
        $fullName  = $staffInfo ? $this->buildFullName($staffInfo) : null;

        $updated = [];
        $skipped = [];
        $errors  = [];

        DB::transaction(function () use (
            $itemIds, $roomInfo, $staffInfo, $fullName,
            &$updated, &$skipped, &$errors
        ) {
            $items = ItemsModel::whereIn('id', $itemIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($itemIds as $itemId) {
                /** @var ItemsModel|null $item */
                $item = $items->get($itemId);

                if (! $item) {
                    $skipped[] = ['id' => $itemId, 'reason' => 'Item topilmadi.'];
                    continue;
                }

                try {
                    DB::transaction(function () use (
                        $item, $roomInfo, $staffInfo, $fullName,
                        &$updated
                    ) {
                        $changes = $this->buildChanges($item, $roomInfo, $staffInfo, $fullName);

                        if (empty($changes)) {
                            // Hech narsa o'zgarmagan — skipped ichiga qo'shiladi (pastda)
                            return;
                        }

                        $item->update($changes);

                        $updated[] = [
                            'item_id'          => $item->id,
                            'inventory_number' => $item->inventory_number,
                            'changes'          => array_keys($changes),
                        ];
                    });

                    // Agar updated massiviga qo'shilmagan bo'lsa — hech narsa o'zgarmagan
                    $wasUpdated = collect($updated)->contains('item_id', $item->id);
                    if (! $wasUpdated) {
                        $skipped[] = [
                            'id'               => $item->id,
                            'inventory_number' => $item->inventory_number,
                            'reason'           => 'Ma\'lumotlar avvalgi qiymat bilan bir xil, o\'zgartirish kerak emas.',
                        ];
                    }

                } catch (\Exception $e) {
                    Log::error('EditItemStaffService: item yangilanmadi.', [
                        'item_id' => $itemId,
                        'error'   => $e->getMessage(),
                    ]);

                    $errors[] = [
                        'id'    => $itemId,
                        'error' => $e->getMessage(),
                    ];
                }
            }

            if (empty($updated) && ! empty($errors)) {
                throw new \RuntimeException(
                    'Barcha itemlarda xatolik yuz berdi. Tahrirlash bekor qilindi.'
                );
            }
        });

        return [
            'updated' => $updated,
            'skipped' => $skipped,
            'errors'  => $errors,
            'summary' => [
                'total'   => count($itemIds),
                'updated' => count($updated),
                'skipped' => count($skipped),
                'errors'  => count($errors),
            ],
        ];
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /**
     * Kamida bitta maydon (xona yoki xodim) berilganligini tekshiradi.
     *
     * @throws \InvalidArgumentException
     */
    private function validateParams(array $params): void
    {
        $hasRoom  = ! empty($params['room_name']);
        $hasStaff = ! empty($params['last_name']) || ! empty($params['staff_id']);

        if (! $hasRoom && ! $hasStaff) {
            throw new \InvalidArgumentException(
                'Kamida xona (room_name) yoki xodim (last_name / staff_id) ma\'lumoti berilishi shart.'
            );
        }
    }

    /**
     * Agar room_name berilgan bo'lsa API dan xona ma'lumotini oladi.
     * Berilmagan bo'lsa null qaytaradi.
     *
     * @throws RoomApiException
     */
    private function resolveRoomInfo(array $params): ?array
    {
        if (empty($params['room_name'])) {
            return null;
        }

        return $this->roomApiService->getRoomData($params['room_name']);
    }

    /**
     * Agar xodim parametrlari berilgan bo'lsa API dan xodim ma'lumotini oladi.
     * Berilmagan bo'lsa null qaytaradi.
     *
     * @throws ApiConnectionException
     * @throws ApiRequestFailedException
     * @throws ApiInvalidResponseException
     * @throws \RuntimeException
     */
    private function resolveStaffInfo(array $params): ?array
    {
        $hasStaff = ! empty($params['last_name']) || ! empty($params['staff_id']);

        if (! $hasStaff) {
            return null;
        }

        // staff_id orqali to'g'ridan-to'g'ri qidirish imkoni bo'lsa
        if (! empty($params['staff_id']) && empty($params['last_name'])) {
            throw new \InvalidArgumentException(
                'staff_id bilan birga last_name ham berilishi shart (API qidiruvi uchun).'
            );
        }

        $lastName = $params['last_name'];
        $results  = $this->staffApiService->searchStaff($lastName);

        if (empty($results)) {
            throw new \RuntimeException("'{$lastName}' familiyali xodim topilmadi.");
        }

        // staff_id bo'yicha aniq moslik
        if (! empty($params['staff_id'])) {
            $needle = (string) $params['staff_id'];
            foreach ($results as $s) {
                if ((string) ($s['id'] ?? '') === $needle) {
                    return $s;
                }
            }
            throw new \RuntimeException("ID={$needle} li xodim qidiruv natijalarida topilmadi.");
        }

        // Ism va otasining ismi bo'yicha aniqlashtirish
        $hasFirst  = ! empty($params['first_name']);
        $hasMiddle = ! empty($params['middle_name']);

        if ($hasFirst || $hasMiddle) {
            foreach ($results as $s) {
                $firstOk  = ! $hasFirst  || mb_strtolower(trim($s['first_name']  ?? '')) === mb_strtolower(trim($params['first_name']));
                $middleOk = ! $hasMiddle || mb_strtolower(trim($s['middle_name'] ?? '')) === mb_strtolower(trim($params['middle_name']));
                if ($firstOk && $middleOk) {
                    return $s;
                }
            }
        }

        // Birinchi natijani qaytarish
        return $results[0];
    }

    /**
     * Faqat o'zgargan maydonlarni qaytaradi.
     * Bu keraksiz DB UPDATE larini oldini oladi.
     */
    private function buildChanges(
        ItemsModel $item,
        ?array $roomInfo,
        ?array $staffInfo,
        ?string $fullName
    ): array {
        $changes = [];

        // --- Xona ma'lumotlari ---
        if ($roomInfo !== null) {
            if ($item->room_name !== ($roomInfo['room_name'] ?? null)) {
                $changes['room_name'] = $roomInfo['room_name'];
            }
            if ($item->building !== ($roomInfo['building'] ?? null)) {
                $changes['building'] = $roomInfo['building'];
            }
            if ($item->room_number !== ($roomInfo['room_number'] ?? null)) {
                $changes['room_number'] = $roomInfo['room_number'];
            }
        }

        // --- Xodim ma'lumotlari ---
        if ($staffInfo !== null) {
            if ($item->last_name !== ($staffInfo['last_name'] ?? null)) {
                $changes['last_name'] = $staffInfo['last_name'];
            }
            if ($item->first_name !== ($staffInfo['first_name'] ?? null)) {
                $changes['first_name'] = $staffInfo['first_name'];
            }
            if ($item->middle_name !== ($staffInfo['middle_name'] ?? null)) {
                $changes['middle_name'] = $staffInfo['middle_name'];
            }
            if ($fullName !== null && $item->full_name !== $fullName) {
                $changes['full_name'] = $fullName;
            }
            if (isset($staffInfo['department']) && $item->department !== $staffInfo['department']) {
                $changes['department'] = $staffInfo['department'];
            }
        }

        return $changes;
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
}
