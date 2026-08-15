<?php

namespace App\Http\Requests\WarehouseBatch;

use Illuminate\Foundation\Http\FormRequest;

class StoreWarehouseBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'batch_number' => ['required', 'string', 'max:50', 'unique:warehouse_batches,batch_number'],

            'signers' => ['required', 'array', 'min:1'],
            'signers.*.staff_id' => ['required', 'integer', 'distinct'],
            'signers.*.full_name' => ['required', 'string', 'max:255'],
        ];
    }
}
