<?php
// App/Http/Requests/Device/TransferWarehouseRequest.php
namespace App\Http\Requests\Device;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TransferWarehouseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'warehouse_ids'    => ['required', 'array', 'min:1'],
            'warehouse_ids.*'  => ['required', 'integer', 'exists:warehouse,id'],
            'inventory_number' => ['required', 'string', 'unique:inventorynumbers,inventory_number'],
            'room_id'          => ['nullable', 'integer'],
            'employee_id'      => ['nullable', 'integer'],
        ];
    }
}
