<?php

namespace App\Services\Transfer;

use App\Models\UzasboImportModel;
use InvalidArgumentException;

class TransferService
{
    /**
     * category_id => Model class
     * Yangi category qo'shish uchun shu yerga 1 qator qo'shing
     */
    private array $categoryModelMap = [
        1 => \App\Models\Computermodel::class,
        2 => \App\Models\Printermodel::class,
    ];

    /**
     * Batch transfer
     *
     * @param array $items  [ ['import_id' => int, 'payload' => array] ]
     * @return array        [ 'success' => [...], 'failed' => [...] ]
     */
    public function transferBatch(array $items): array
    {
        $results = ['success' => [], 'failed' => []];

        foreach ($items as $item) {
            try {
                $import  = UzasboImportModel::findOrFail($item['import_id']);
                $created = $this->transfer($import, $item['payload']);

                $results['success'][] = [
                    'import_id'   => $import->id,
                    'created_id'  => $created->id,
                    'category_id' => $item['payload']['category_id'],
                ];
            } catch (\Throwable $e) {
                $results['failed'][] = [
                    'import_id' => $item['import_id'] ?? null,
                    'error'     => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * Bitta yozuvni transfer qiladi
     */
    private function transfer(UzasboImportModel $import, array $payload): mixed
    {
        $categoryId = $payload['category_id']
            ?? throw new InvalidArgumentException('category_id majburiy');

        $modelClass = $this->categoryModelMap[$categoryId]
            ?? throw new InvalidArgumentException(
                "category_id={$categoryId} uchun model topilmadi"
            );

        return $modelClass::create([
            // --- Uzasbodan ---
            'name'                => $import->name,
            'inventory_number_id' => null,

            // --- Default ---
            'quantity'            => 1,
            'description'         => null,

            // --- Payloaddan ---
            // category_id, model_id, unit_id, room_name, building, room_number
            ...$payload,
        ]);
    }
}
