<?php
// App/Http/Requests/StoreNetworkDeviceRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNetworkDeviceRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'             => ['required', 'string', 'max:255'],
            'category_id'      => ['required', 'integer', 'exists:categories,id'],
            'model_id'         => ['required', 'integer', 'exists:models,id'],
            'unit_id'          => ['required', 'integer', 'exists:units,id'],
            'quantity'         => ['required', 'integer', 'min:1'],
            'description'      => ['required', 'string'],
            'inventory_number' => ['required', 'integer', 'unique:inventorynumbers,inventory_number'],
            'room_name'        => ['required', 'string', 'max:255'],
            'room_number'      => ['required', 'string', 'max:100'],
            'building'         => ['required', 'string', 'max:255'],
            'employee_id'      => ['nullable', 'integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'           => 'Nomi majburiy.',
            'category_id.exists'      => 'Kategoriya topilmadi.',
            'model_id.exists'         => 'Model topilmadi.',
            'unit_id.exists'          => 'O\'lchov birligi topilmadi.',
            'quantity.min'            => 'Miqdor kamida 1 bo\'lishi kerak.',
            'inventory_number.unique' => 'Bu inventar raqami allaqachon mavjud.',
            'room_name.required'      => 'Xona nomi majburiy.',
            'room_number.required'    => 'Xona raqami majburiy.',
            'building.required'       => 'Bino majburiy.',
        ];
    }
}
