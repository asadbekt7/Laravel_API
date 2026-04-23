<?php

namespace App\Http\Requests\Computer;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TransferWarehouseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
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
