<?php

namespace App\Http\Requests\Warehouse;

use App\Enums\ItemType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateWarehouseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'information_id' => ['nullable', 'exists:information,id'],
            'name'           => ['sometimes', 'string', 'max:255'],
            'item_type'      => ['sometimes', Rule::enum(ItemType::class)],
            'expiry_date'    => [
                'required_if:item_type,' . ItemType::ASOSIY_VOSITA->value,
                'nullable',
                'date',
            ],
            'statya' => [
                'required_if:item_type,' . ItemType::RASXOD->value,
                'nullable',
                'string',
                'max:255',
            ],
            'type_id'        => ['sometimes', 'exists:types,id'],
            'category_id'    => ['sometimes', 'exists:categories,id'],
            'model_id'       => ['sometimes', 'exists:models,id'],
            'quantity'       => ['sometimes', 'integer', 'min:0'],
            'unit_id'        => ['sometimes', 'exists:units,id'],
            'location_id'    => ['sometimes', 'exists:locations,id'],
            'product_price'  => ['sometimes', 'numeric', 'min:0'],
            'description'    => ['sometimes', 'string'],
        ];
    }
}
