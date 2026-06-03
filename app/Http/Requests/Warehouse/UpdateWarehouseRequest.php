<?php

namespace App\Http\Requests\Warehouse;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWarehouseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'receiving_id'            => ['sometimes', 'integer', 'exists:receivings,id'],
            'name'                    => ['sometimes', 'string', 'max:255'],
            'type_id'                 => ['sometimes', 'integer', 'exists:types,id'],
            'category_id'             => ['sometimes', 'integer', 'exists:categories,id'],
            'model_id'                => ['sometimes', 'integer', 'exists:models,id'],
            'receiving_supplier_name' => ['sometimes', 'integer', 'exists:receivings,id'],
            'quantity'                => ['sometimes', 'integer', 'min:0'],
            'unit_id'                 => ['sometimes', 'integer', 'exists:units,id'],
            'condition'               => ['sometimes', 'string', 'in:new,used,damaged'],
            'location_id'             => ['sometimes', 'integer', 'exists:locations,id'],
            'staff_id'                => ['sometimes', 'integer'],
            'price_per_unit'          => ['sometimes', 'numeric', 'min:0'],
            'product_price'           => ['sometimes', 'numeric', 'min:0'],
            'description'             => ['sometimes', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'receiving_id.exists'              => 'Tanlangan receiving mavjud emas.',
            'type_id.exists'                   => 'Tanlangan tur mavjud emas.',
            'category_id.exists'               => 'Tanlangan kategoriya mavjud emas.',
            'model_id.exists'                  => 'Tanlangan model mavjud emas.',
            'receiving_supplier_name.exists'   => 'Tanlangan supplier mavjud emas.',
            'quantity.integer'                 => 'Miqdor butun son bo\'lishi kerak.',
            'unit_id.exists'                   => 'Tanlangan birlik mavjud emas.',
            'condition.in'                     => 'Holat: new, used yoki damaged bo\'lishi kerak.',
            'location_id.exists'               => 'Tanlangan joylashuv mavjud emas.',
            'price_per_unit.numeric'           => 'Birlik narxi raqam bo\'lishi kerak.',
            'product_price.numeric'            => 'Mahsulot narxi raqam bo\'lishi kerak.',
        ];
    }
}
