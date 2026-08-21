<?php

declare(strict_types=1);

namespace App\Http\Requests\Receiving;

use Illuminate\Foundation\Http\FormRequest;

class ReceivingIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // yoki: $this->user()->can('viewAny', InformationModel::class);
    }

    public function rules(): array
    {
        return [
            'search'      => ['nullable', 'string', 'max:255'],
            'supplier_id' => ['nullable', 'integer', 'exists:suppliers,id'],
            'from_date'   => ['nullable', 'date'],
            'to_date'     => ['nullable', 'date', 'after_or_equal:from_date'],
            'per_page'    => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function attributes(): array
    {
        return [
            'search'      => 'qidiruv',
            'supplier_id' => 'yetkazib beruvchi',
            'from_date'   => 'boshlanish sanasi',
            'to_date'     => 'tugash sanasi',
            'per_page'    => 'sahifadagi soni',
        ];
    }

    public function messages(): array
    {
        return [
            'required'        => ':attribute majburiy.',
            'date'            => ':attribute to\'g\'ri sana formatida bo\'lishi kerak.',
            'after_or_equal'  => ':attribute "boshlanish sanasi"dan oldin bo\'lmasligi kerak.',
            'integer'         => ':attribute butun son bo\'lishi kerak.',
            'exists'          => 'Tanlangan :attribute mavjud emas.',
            'max'             => ':attribute belgilangan chegaradan oshmasligi kerak.',
        ];
    }
}
