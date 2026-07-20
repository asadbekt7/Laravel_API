<?php

namespace App\Http\Requests\Warehouse;

use App\Enums\ItemType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'item_type'   => ['required', Rule::enum(ItemType::class)],
            'expiry_date' => [
                'required_if:item_type,' . ItemType::ASOSIY_VOSITA->value,
                'nullable',
                'date',
                'after:today',
            ],
            'statya' => [
                'required_if:item_type,' . ItemType::RASXOD->value,
                'nullable',
                'string',
                'max:255',
            ],
            'type_id'     => ['required', 'exists:types,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'model_id'    => ['required', 'exists:models,id'],
            'location_id' => ['required', 'exists:locations,id'],
            'condition'   => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'information_id' => 'ma\'lumot',
            'item_type'      => 'mahsulot turi',
            'expiry_date'    => 'yaroqlilik muddati',
            'statya'         => 'statya',
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
            'required_if' => ':attribute majburiy.',
            'integer'     => ':attribute butun son bo\'lishi kerak.',
            'exists'      => 'Tanlangan :attribute mavjud emas.',
            'unique'      => 'Bu :attribute allaqachon omborga kirim qilingan.',
            'string'      => ':attribute matn bo\'lishi kerak.',
            'date'        => ':attribute to\'g\'ri sana bo\'lishi kerak.',
            'after'       => ':attribute bugundan keyingi sana bo\'lishi kerak.',
            'enum'        => 'Tanlangan :attribute noto\'g\'ri.',
        ];
    }
}
