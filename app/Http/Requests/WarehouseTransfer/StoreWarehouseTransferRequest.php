<?php

namespace App\Http\Requests\WarehouseTransfer;

use Illuminate\Foundation\Http\FormRequest;

class StoreWarehouseTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'staff'             => ['required', 'array'],
            'staff.last_name'   => ['required', 'string', 'max:255'],
            'staff.first_name'  => ['required', 'string', 'max:255'],
            'staff.middle_name' => ['nullable', 'string', 'max:255'],
            'staff.full_name'   => ['required', 'string', 'max:255'],
            'staff.department'  => ['nullable', 'string', 'max:255'],

            'products'                    => ['required', 'array', 'min:1'],
            'products.*.warehouse_id'     => ['required', 'integer', 'exists:warehouse,id', 'distinct'],
            'products.*.quantity'         => ['required', 'integer', 'min:1'],
            'products.*.inventory_number' => ['required', 'string', 'max:255', 'distinct', 'unique:items,inventory_number'],
            'products.*.room_name'        => ['nullable', 'string', 'max:255'],
            'products.*.building'         => ['nullable', 'string', 'max:255'],
            'products.*.room_number'      => ['nullable', 'string', 'max:255'],
            'products.*.condition'        => ['nullable', 'in:new,good,fair,poor,damaged'],
            'products.*.notes'            => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'staff.required'                       => 'Xodim tanlanishi shart.',
            'products.required'                    => 'Kamida bitta mahsulot tanlanishi kerak.',
            'products.*.warehouse_id.distinct'     => 'Bitta mahsulot ikki marta tanlangan.',
            'products.*.quantity.min'              => 'Miqdor kamida 1 bo\'lishi kerak.',
            'products.*.inventory_number.unique'   => 'Bu inventar raqami allaqachon mavjud.',
            'products.*.inventory_number.distinct' => 'Inventar raqamlari takrorlanmasligi kerak.',
        ];
    }
}
