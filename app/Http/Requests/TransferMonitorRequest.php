<?php
// App/Http/Requests/TransferMonitorRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferMonitorRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'warehouse_id'     => ['required', 'integer', 'exists:warehouse,id'],
            'inventory_number' => ['required', 'integer', 'unique:inventorynumbers,inventory_number'],
            'room_name'        => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'warehouse_id.required'     => 'Warehouse ID majburiy.',
            'warehouse_id.exists'       => 'Ko\'rsatilgan warehouse topilmadi.',
            'inventory_number.required' => 'Inventar raqami majburiy.',
            'inventory_number.unique'   => 'Bu inventar raqami allaqachon mavjud.',
            'room_name.required'        => 'Xona nomi majburiy.',
        ];
    }
}
