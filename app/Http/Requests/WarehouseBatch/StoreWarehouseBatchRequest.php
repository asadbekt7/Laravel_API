<?php
// app/Http/Requests/WarehouseBatch/StoreWarehouseBatchRequest.php

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

            'signer_ids' => ['required', 'array', 'min:1'],
            'signer_ids.*' => ['integer', 'distinct', 'exists:users,id'],
        ];
    }
}
