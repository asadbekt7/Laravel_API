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
            'receiving_id'            => ['required', 'integer', 'exists:receivings,id'],
            'name'                    => ['required', 'string', 'max:255'],
            'type_id'                 => ['required', 'integer', 'exists:types,id'],
            'category_id'             => ['required', 'integer', 'exists:categories,id'],
            'model_id'                => ['required', 'integer', 'exists:models,id'],
            'receiving_supplier_name' => ['required', 'integer', 'exists:receivings,id'],
            'quantity'                => ['required', 'integer', 'min:0'],
            'unit_id'                 => ['required', 'integer', 'exists:units,id'],
            'condition'               => ['sometimes', 'string', 'in:new,used,damaged'],
            'location_id'             => ['required', 'integer', 'exists:locations,id'],
            'staff_id'                => ['required', 'integer'],
            'price_per_unit'          => ['sometimes', 'numeric', 'min:0'],
            'product_price'           => ['required', 'numeric', 'min:0'],
            'description'             => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'receiving_id.required'            => 'Receiving ID majburiy.',
            'receiving_id.exists'              => 'Tanlangan receiving mavjud emas.',
            'name.required'                    => 'Nomi majburiy.',
            'type_id.required'                 => 'Turi majburiy.',
            'type_id.exists'                   => 'Tanlangan tur mavjud emas.',
            'category_id.required'             => 'Kategoriya majburiy.',
            'category_id.exists'               => 'Tanlangan kategoriya mavjud emas.',
            'model_id.required'                => 'Model majburiy.',
            'model_id.exists'                  => 'Tanlangan model mavjud emas.',
            'receiving_supplier_name.required' => 'Supplier majburiy.',
            'receiving_supplier_name.exists'   => 'Tanlangan supplier mavjud emas.',
            'quantity.required'                => 'Miqdor majburiy.',
            'quantity.integer'                 => 'Miqdor butun son bo\'lishi kerak.',
            'unit_id.required'                 => 'O\'lchov birligi majburiy.',
            'unit_id.exists'                   => 'Tanlangan birlik mavjud emas.',
            'condition.in'                     => 'Holat: new, used yoki damaged bo\'lishi kerak.',
            'location_id.required'             => 'Joylashuv majburiy.',
            'location_id.exists'               => 'Tanlangan joylashuv mavjud emas.',
            'staff_id.required'                => 'Xodim ID majburiy.',
            'price_per_unit.numeric'           => 'Birlik narxi raqam bo\'lishi kerak.',
            'product_price.required'           => 'Mahsulot narxi majburiy.',
            'product_price.numeric'            => 'Mahsulot narxi raqam bo\'lishi kerak.',
            'description.required'             => 'Tavsif majburiy.',
        ];
    }
}
