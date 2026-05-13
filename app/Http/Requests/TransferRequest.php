<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items'                       => ['required', 'array', 'min:1'],
            'items.*.import_id'           => ['required', 'integer', 'exists:uzasbo_imports,id'],
            'items.*.payload'             => ['required', 'array'],
            'items.*.payload.category_id' => ['required', 'integer', 'exists:categories,id'],
            'items.*.payload.model_id'    => ['required', 'integer'],
            'items.*.payload.room_name'   => ['required', 'string', 'max:255'],
            'items.*.payload.building'    => ['nullable', 'string', 'max:255'],
            'items.*.payload.room_number' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required'                       => 'items massivi bo\'sh bo\'lmasligi kerak',
            'items.*.import_id.exists'             => 'uzasbo_imports da bunday yozuv topilmadi',
            'items.*.payload.category_id.required' => 'category_id majburiy',
            'items.*.payload.category_id.exists'   => 'Bunday category mavjud emas',
            'items.*.payload.model_id.required'    => 'model_id majburiy',
            'items.*.payload.room_name.required'   => 'room_name majburiy',
        ];
    }
}
