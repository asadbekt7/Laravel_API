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
            'information_id' => ['nullable', 'exists:information,id'],
            'name'           => ['required', 'string', 'max:255'],
            'type_id'        => ['required', 'exists:types,id'],
            'category_id'    => ['required', 'exists:categories,id'],
            'model_id'       => ['required', 'exists:models,id'],
            'quantity'       => ['required', 'integer', 'min:0'],
            'unit_id'        => ['required', 'exists:units,id'],
            'location_id'    => ['required', 'exists:locations,id'],
            'product_price'  => ['required', 'numeric', 'min:0'],
            'description'    => ['required', 'string'],
        ];
    }
}
