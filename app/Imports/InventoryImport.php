<?php

namespace App\Imports;

use App\Models\UzasboImportModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

/**
 * col[0]  → row_number
 * col[1]  → account_number       (string, faqat raqamlar, max 27)
 * col[2]  → full_name
 * col[3]  → department
 * col[4]  → bank_schot
 * col[5]  → inventory_number     (string, aynan 14 raqam, UNIQUE)
 * col[6]  → old_inventory_number (nullable)
 * col[7]  → expense_article
 * col[8]  → name                 (majburiy)
 * col[9]  → unit
 * col[10] → opening_quantity     (int/float)
 * col[11] → opening_summ         (float)
 * col[12] → debit_quantity
 * col[13] → debit_summ
 * col[14] → credit_quantity
 * col[15] → credit_summ
 * col[16] → closing_quantity
 * col[17] → closing_summ
 */
class InventoryImport implements
    ToCollection,
    WithChunkReading,
    SkipsEmptyRows
{
    private const HEADER_ROWS = 3;
    private const CHUNK_SIZE = 500;

    private int   $importedCount     = 0;
    private int   $skippedCount      = 0;
    private array $errors            = [];
    private array $seenInThisImport  = [];
    private ?string $importType;
    public function __construct(?string $importType = null)
    {
        $this->importType = $importType;
    }
    public function chunkSize(): int
    {
        return self::CHUNK_SIZE;
    }
    public function collection(Collection $rows): void
    {
        $inserts = [];

        foreach ($rows as $index => $row) {
            if ($index < self::HEADER_ROWS) {
                continue;
            }

            $rowNum  = $index + 1;
            $r       = $row->toArray();
            if ($this->isEmptyRow($r)) {
                continue;
            }
            $data = $this->mapRow($r, $rowNum);
            $validator = Validator::make($data, $this->rules());

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $message) {
                    $this->errors[] = "Row {$rowNum}: {$message}";
                }
                $this->skippedCount++;
                continue;
            }
            $inv = $data['inventory_number'];

            if (isset($this->seenInThisImport[$inv])) {
                $this->errors[]  = "Row {$rowNum}: '{$inv}' bu faylda takrorlanmoqda — qator o'tkazib yuborildi.";
                $this->skippedCount++;
                continue;
            }
            if (UzasboImportModel::where('inventory_number', $inv)->exists()) {
                $this->errors[]  = "Row {$rowNum}: '{$inv}' bazada allaqachon mavjud — qator o'tkazib yuborildi.";
                $this->skippedCount++;
                continue;
            }
            $this->seenInThisImport[$inv] = true;

            $inserts[] = array_merge($data, [
                'import_type' => $this->importType,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
        if (!empty($inserts)) {
            DB::table('uzasbo_imports')->insert($inserts);
            $this->importedCount += count($inserts);
        }
    }
    private function isEmptyRow(array $r): bool
    {
        $invNum = isset($r[5]) ? trim((string) $r[5]) : '';
        $name   = isset($r[8]) ? trim((string) $r[8]) : '';

        return $invNum === '' && $name === '';
    }

    /**
     * col[0]  → row_number
     * col[1]  → account_number
     * col[2]  → full_name
     * col[3]  → department
     * col[4]  → bank_schot
     * col[5]  → inventory_number     ← UNIQUE, 14 raqam
     * col[6]  → old_inventory_number
     * col[7]  → expense_article
     * col[8]  → name                 ← majburiy
     * col[9]  → unit
     * col[10] → opening_quantity     (int — Excel da 1)
     * col[11] → opening_summ         (float — Excel da 7304855.48)
     * col[12] → debit_quantity
     * col[13] → debit_summ
     * col[14] → credit_quantity
     * col[15] → credit_summ
     * col[16] → closing_quantity
     * col[17] → closing_summ
     */
    private function mapRow(array $r, int $rowNum): array
    {
        return [
            'row_number'           => $this->str($r, 0),
            'account_number'       => $this->str($r, 1),
            'full_name'            => $this->str($r, 2),
            'department'           => $this->str($r, 3),
            'bank_schot'           => $this->str($r, 4),
            'inventory_number'     => $this->str($r, 5),
            'old_inventory_number' => $this->str($r, 6),
            'expense_article'      => $this->str($r, 7),
            'name'                 => $this->str($r, 8),
            'unit'                 => $this->str($r, 9),
            'opening_quantity'     => $this->num($r, 10),
            'opening_summ'         => $this->num($r, 11),
            'debit_quantity'       => $this->num($r, 12),
            'debit_summ'           => $this->num($r, 13),
            'credit_quantity'      => $this->num($r, 14),
            'credit_summ'          => $this->num($r, 15),
            'closing_quantity'     => $this->num($r, 16),
            'closing_summ'         => $this->num($r, 17),
        ];
    }
    private function rules(): array
    {
        return [
            'inventory_number' => ['required', 'string', 'regex:/^\d{14}$/'],
            'name'             => ['required', 'string', 'max:1000'],
            'account_number'   => ['nullable', 'string', 'regex:/^\d+$/', 'max:27'],
            'bank_schot'       => ['nullable', 'string', 'max:50'],
            'unit'             => ['nullable', 'string', 'max:50'],
            'opening_summ'     => ['nullable', 'numeric', 'min:0'],
            'closing_summ'     => ['nullable', 'numeric', 'min:0'],
            'debit_summ'       => ['nullable', 'numeric'],
            'credit_summ'      => ['nullable', 'numeric'],
        ];
    }
    private function str(array $r, int $index): ?string
    {
        if (!isset($r[$index])) {
            return null;
        }
        $val = trim((string) $r[$index]);
        return $val !== '' ? $val : null;
    }
    private function num(array $r, int $index): ?float
    {
        if (!isset($r[$index])) {
            return null;
        }
        return is_numeric($r[$index]) ? (float) $r[$index] : null;
    }
    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
