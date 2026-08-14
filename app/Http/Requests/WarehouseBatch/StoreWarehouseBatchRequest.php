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
            'staff_id' => ['required', 'integer', 'exists:users,id'],

            'products' => ['required', 'array', 'min:1'],
            'products.*.warehouse_id' => ['required', 'integer', 'exists:warehouse,id', 'distinct'],
            'products.*.quantity' => ['required', 'integer', 'min:1'],

            'signer_ids' => ['required', 'array', 'min:1'],
            'signer_ids.*' => ['integer', 'distinct', 'exists:users,id'],
        ];
    }
}
