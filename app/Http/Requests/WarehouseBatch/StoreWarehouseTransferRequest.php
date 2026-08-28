<?php
// app/Http/Requests/WarehouseBatch/StoreWarehouseTransferRequest.php

namespace App\Http\Requests\WarehouseBatch;

use Illuminate\Foundation\Http\FormRequest;

class StoreWarehouseTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'batch_id' => ['required', 'string', 'exists:warehouse_batches,batch_number'],

            'staff.last_name' => ['required', 'string', 'max:255'],
            'staff.first_name' => ['required', 'string', 'max:255'],
            'staff.middle_name' => ['nullable', 'string', 'max:255'],
            'staff.full_name' => ['required', 'string', 'max:255'],
            'staff.department' => ['nullable', 'string', 'max:255'],

            'products' => ['required', 'array', 'min:1'],
            'products.*.warehouse_item_id' => ['required', 'integer', 'exists:warehouse_items,id', 'distinct'],
            'products.*.quantity' => ['required', 'integer', 'min:1'],
            'products.*.room_name' => ['nullable', 'string'],
            'products.*.building' => ['nullable', 'string'],
            'products.*.room_number' => ['nullable', 'string'],
            'products.*.condition' => ['nullable', 'string'],
            'products.*.notes' => ['nullable', 'string'],
        ];
    }
}
