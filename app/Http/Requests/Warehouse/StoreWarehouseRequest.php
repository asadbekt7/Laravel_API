<?php

namespace App\Http\Requests\Warehouse;

use Illuminate\Foundation\Http\FormRequest;

class StoreWarehouseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'information_id' => [
                'required', 'integer',
                'exists:information,id',
                'unique:warehouse,information_id', // bitta information faqat bir marta kirim qilinadi
            ],
            // item_type / expiry_date / statya OLIB TASHLANDI —
            // ular warehouse'da yo'q, buxgalter keyin to'ldiradi.
            'type_id'     => ['required', 'exists:types,id'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'model_id'    => ['nullable', 'exists:models,id'],
            'location_id' => ['required', 'exists:locations,id'],
            'condition'   => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'information_id' => 'ma\'lumot',
            'type_id'        => 'turi',
            'category_id'    => 'kategoriya',
            'model_id'       => 'model',
            'location_id'    => 'ombor manzili',
            'condition'      => 'holati',
            'description'    => 'izoh',
        ];
    }

    public function messages(): array
    {
        return [
            'required'    => ':attribute majburiy.',
            'integer'     => ':attribute butun son bo\'lishi kerak.',
            'exists'      => 'Tanlangan :attribute mavjud emas.',
            'unique'      => 'Bu :attribute allaqachon omborga kirim qilingan.',
            'string'      => ':attribute matn bo\'lishi kerak.',
        ];
    }
}
