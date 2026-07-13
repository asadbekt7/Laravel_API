<?php

namespace App\Http\Requests\Warehouse;

use Illuminate\Foundation\Http\FormRequest;

class BulkStoreWarehouseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'information_id'         => ['required', 'integer', 'exists:information,id'],

            'items'                  => ['required', 'array', 'min:1'],
            'items.*.name'           => ['required', 'string', 'max:255'],
            'items.*.type_id'        => ['required', 'integer', 'exists:types,id'],
            'items.*.category_id'    => ['required', 'integer', 'exists:categories,id'],
            'items.*.model_id'       => ['required', 'integer', 'exists:models,id'],
            'items.*.quantity'       => ['required', 'integer', 'min:0'],
            'items.*.unit_id'        => ['required', 'integer', 'exists:units,id'],
            'items.*.location_id'    => ['required', 'integer', 'exists:locations,id'],
            'items.*.product_price'  => ['required', 'numeric', 'min:0'],
            'items.*.description'    => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'information_id.required'        => 'information_id majburiy.',
            'information_id.exists'          => 'Bunday information mavjud emas.',

            'items.required'                 => 'Mahsulotlar ro\'yxati majburiy.',
            'items.min'                      => 'Kamida 1 ta mahsulot bo\'lishi kerak.',

            'items.*.name.required'          => 'Mahsulot nomi majburiy.',
            'items.*.type_id.required'       => 'Turi majburiy.',
            'items.*.type_id.exists'         => 'Bunday tur mavjud emas.',
            'items.*.category_id.required'   => 'Kategoriya majburiy.',
            'items.*.category_id.exists'     => 'Bunday kategoriya mavjud emas.',
            'items.*.model_id.required'      => 'Model majburiy.',
            'items.*.model_id.exists'        => 'Bunday model mavjud emas.',
            'items.*.quantity.required'      => 'Miqdori majburiy.',
            'items.*.quantity.min'           => 'Miqdor 0 dan kichik bo\'lmasin.',
            'items.*.unit_id.required'       => 'O\'lchov birligi majburiy.',
            'items.*.unit_id.exists'         => 'Bunday o\'lchov birligi mavjud emas.',
            'items.*.location_id.required'   => 'Joylashuv majburiy.',
            'items.*.location_id.exists'     => 'Bunday joylashuv mavjud emas.',
            'items.*.product_price.required' => 'Narxi majburiy.',
            'items.*.product_price.numeric'  => 'Narx raqam bo\'lishi kerak.',
            'items.*.product_price.min'      => 'Narx manfiy bo\'lmasin.',
        ];
    }
}
