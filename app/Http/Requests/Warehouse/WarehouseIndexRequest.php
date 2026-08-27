<?php

declare(strict_types=1);

namespace App\Http\Requests\Warehouse;

use App\Enums\WarehouseAktStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class WarehouseIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null; // aniq ruxsat 'perm' middleware'da tekshiriladi (sizning boshqa Request'laringiz kabi)
    }

    public function rules(): array
    {
        return [
            'akt_number'    => ['sometimes', 'string', 'max:255'],
            'akt_date_from' => ['sometimes', 'date'],
            'akt_date_to'   => ['sometimes', 'date', 'after_or_equal:akt_date_from'],
            // Hozircha barcha warehouse qatorlari ACCEPTED holatda yaratiladi (chiqim
            // jarayoni hali yozilmagan), lekin filtr kelajakka moslab qo'yildi.
            'status'        => ['sometimes', new Enum(WarehouseAktStatus::class)],
            'location_id'   => ['sometimes', 'integer', 'exists:locations,id'],
            'per_page'      => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function attributes(): array
    {
        return [
            'akt_number'    => 'akt raqami',
            'akt_date_from' => 'akt sanasi (dan)',
            'akt_date_to'   => 'akt sanasi (gacha)',
            'location_id'   => 'ombor manzili',
        ];
    }
}
