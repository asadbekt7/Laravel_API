<?php

namespace App\Http\Requests;

use App\Enums\ItemType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompleteBugalteriyaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $itemType = $this->input('item_type');

        // item_type doim majburiy — buxgalter tanlaydi
        $rules = [
            'item_type' => ['required', Rule::enum(ItemType::class)],
        ];

        // ===== ASOSIY VOSITA: expiry_date + inventory_number majburiy =====
        if ($itemType === ItemType::ASOSIY_VOSITA->value) {
            $rules['expiry_date']      = ['required', 'date'];
            $rules['inventory_number'] = ['required', 'string', 'max:255'];
        }

        // ===== RASXOD: faqat statya majburiy =====
        if ($itemType === ItemType::RASXOD->value) {
            $rules['statya'] = ['required', 'string', 'max:255'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'item_type.required'        => 'Buxgalter turini tanlash majburiy.',
            'item_type.enum'            => 'Buxgalter turi noto\'g\'ri.',
            'expiry_date.required'      => 'Asosiy vosita uchun yaroqlilik muddati majburiy.',
            'inventory_number.required' => 'Asosiy vosita uchun inventar raqami majburiy.',
            'statya.required'           => 'Rasxod uchun statya majburiy.',
        ];
    }
}
