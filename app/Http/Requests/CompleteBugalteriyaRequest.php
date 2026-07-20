<?php

namespace App\Http\Requests;

use App\Enums\ItemType;
use Illuminate\Foundation\Http\FormRequest;

class CompleteBugalteriyaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // kerak bo'lsa: $this->user()->can('bugalteriya.complete')
    }

    public function rules(): array
    {
        /** @var \App\Models\BugalteriyaModel $entry */
        $entry = $this->route('bugalteriya');

        // ===== ASOSIY VOSITA: expiry_date + inventory_number majburiy =====
        if ($entry->item_type === ItemType::ASOSIY_VOSITA) {
            return [
                'expiry_date'      => ['required', 'date'],
                'inventory_number' => ['required', 'string', 'max:255'],
            ];
        }

        // ===== RASXOD: faqat statya majburiy =====
        return [
            'statya' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'expiry_date.required'      => 'Asosiy vosita uchun yaroqlilik muddati majburiy.',
            'inventory_number.required' => 'Asosiy vosita uchun inventar raqami majburiy.',
            'statya.required'           => 'Rasxod uchun statya majburiy.',
        ];
    }
}
