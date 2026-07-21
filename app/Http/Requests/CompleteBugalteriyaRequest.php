<?php

namespace App\Http\Requests;

use App\Enums\ItemType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompleteBugalteriyaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // kerak bo'lsa: $this->user()->can('bugalteriya.complete')
    }

    public function rules(): array
    {
        $itemType = $this->input('item_type');

        $rules = [
            // Turni buxgalter tanlaydi — majburiy
            'item_type' => ['required', Rule::in(ItemType::values())],
        ];

        // ===== ASOSIY VOSITA: expiry_date + inventory_number =====
        if ($itemType === ItemType::ASOSIY_VOSITA->value) {
            $rules['expiry_date']      = ['required', 'date'];
            $rules['inventory_number'] = ['required', 'string', 'max:255'];
        }

        // ===== RASXOD: faqat statya =====
        if ($itemType === ItemType::RASXOD->value) {
            $rules['statya'] = ['required', 'string', 'max:255'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'item_type.required'        => 'Mahsulot turini tanlash majburiy (asosiy vosita yoki rasxod).',
            'item_type.in'              => 'Noto\'g\'ri mahsulot turi.',
            'expiry_date.required'      => 'Asosiy vosita uchun yaroqlilik muddati majburiy.',
            'inventory_number.required' => 'Asosiy vosita uchun inventar raqami majburiy.',
            'statya.required'           => 'Rasxod uchun statya majburiy.',
        ];
    }
}
