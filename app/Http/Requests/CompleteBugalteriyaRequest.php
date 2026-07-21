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
        /** @var \App\Models\BugalteriyaModel $entry */
        $entry    = $this->route('bugalteriya');
        $quantity = (int) $entry->quantity;
        $itemType = $this->input('item_type');

        $rules = [
            'item_type' => ['required', Rule::in(ItemType::values())],
        ];

        // ===== ASOSIY VOSITA =====
        // Har bir dona alohida inventar raqam oladi -> miqdor qancha bo'lsa,
        // shuncha inventar raqam kiritiladi.
        if ($itemType === ItemType::ASOSIY_VOSITA->value) {
            $rules['expiry_date'] = ['required', 'date', 'after:today'];

            $rules['inventory_numbers']   = ['required', 'array', "size:{$quantity}"];
            $rules['inventory_numbers.*'] = [
                'required',
                'string',
                'max:255',
                'distinct',                      // massiv ichida takrorlanmasin
                'unique:items,inventory_number', // items'da allaqachon bo'lmasin
            ];
        }

        // ===== RASXOD =====
        // Yaxlit qoladi, faqat statya.
        if ($itemType === ItemType::RASXOD->value) {
            $rules['statya'] = ['required', 'string', 'max:255'];
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'item_type'             => 'mahsulot turi',
            'expiry_date'           => 'yaroqlilik muddati',
            'inventory_numbers'     => 'inventar raqamlari',
            'inventory_numbers.*'   => 'inventar raqami',
            'statya'                => 'statya',
        ];
    }

    public function messages(): array
    {
        return [
            'item_type.required'          => 'Mahsulot turini tanlash majburiy.',
            'item_type.in'                => 'Noto\'g\'ri mahsulot turi.',
            'expiry_date.required'        => 'Asosiy vosita uchun yaroqlilik muddati majburiy.',
            'expiry_date.after'           => 'Yaroqlilik muddati bugundan keyin bo\'lishi kerak.',
            'inventory_numbers.required'  => 'Har bir dona uchun inventar raqami kiritilishi kerak.',
            'inventory_numbers.size'      => 'Inventar raqamlari soni mahsulot miqdoriga teng bo\'lishi kerak.',
            'inventory_numbers.*.required'=> 'Inventar raqami bo\'sh bo\'lishi mumkin emas.',
            'inventory_numbers.*.distinct'=> 'Inventar raqamlari takrorlanmasligi kerak.',
            'inventory_numbers.*.unique'  => 'Bu inventar raqami allaqachon mavjud.',
            'statya.required'             => 'Rasxod uchun statya majburiy.',
        ];
    }
}
