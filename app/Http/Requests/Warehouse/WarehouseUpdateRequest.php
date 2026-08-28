<?php

declare(strict_types=1);

namespace App\Http\Requests\Warehouse;

use App\Models\WarehouseModel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WarehouseUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null; // aniq ruxsat 'perm' middleware'da tekshiriladi
    }

    public function rules(): array
    {
        /** @var WarehouseModel|null $warehouse */
        $warehouse = $this->route('warehouse');

        return [
            'akt_number' => ['required', 'string', 'max:255'],
            'akt_date'   => ['required', 'date', 'before_or_equal:today'],
        ];
    }

    public function attributes(): array
    {
        return [
            'akt_number' => 'akt raqami',
            'akt_date'   => 'akt sanasi',
        ];
    }

    public function messages(): array
    {
        return [
            'akt_number.unique' => 'Bu akt raqami allaqachon boshqa aktda ishlatilgan.',
        ];
    }
}
