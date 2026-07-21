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
            'information_id' => ['nullable', 'exists:information,id'],
            'name'           => ['sometimes', 'string', 'max:255'],
            // item_type / expiry_date / statya OLIB TASHLANDI —
            // ular warehouse'da yo'q, buxgalter keyin to'ldiradi.
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
