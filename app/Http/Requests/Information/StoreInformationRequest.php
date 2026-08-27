<?php

declare(strict_types=1);

namespace App\Http\Requests\Information;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInformationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null; // aniq ruxsat 'perm' middleware'da tekshiriladi
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],

            'contract_number' => ['required', 'string', 'max:255'],
            'contract_date'   => ['required', 'date', 'before_or_equal:today'],
            'contract_file'   => ['required', 'file', 'mimes:pdf', 'max:5120'],

            'supplier_id' => ['required', 'integer', Rule::exists('suppliers', 'id')],

            'bildirishnoma_number' => ['required', 'string', 'max:255'],
            'bildirishnoma_date'   => ['nullable', 'date', 'before_or_equal:today'],
            'bildirishnoma_file'   => ['required', 'file', 'mimes:pdf', 'max:5120'],

            'ishonchnoma_number' => ['required', 'string', 'max:255'],
            'ishonchnoma_date'   => ['required', 'date', 'before_or_equal:today'],
            'ishonchnoma_file'   => ['required', 'file', 'mimes:pdf', 'max:5120'],

            'hisob_faktura'      => ['required', 'string', 'max:255'],
            'hisob_faktura_date' => ['required', 'date', 'before_or_equal:today'],
            'hisob_faktura_file' => ['required', 'file', 'mimes:pdf', 'max:5120'],

            // quantity endi 'integer' EMAS - migratsiyada decimal(20,3)
            'items'                => ['required', 'array', 'min:1', 'max:200'],
            'items.*.product_name' => ['required', 'string', 'max:255'],
            'items.*.unit_id'      => ['required', 'integer', Rule::exists('units', 'id')],
            'items.*.quantity'     => ['required', 'numeric', 'min:0.001'],
            'items.*.item_price'   => ['required', 'numeric', 'min:0', 'max:999999999999.99'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name'                  => 'nomi',
            'contract_number'       => 'shartnoma raqami',
            'contract_date'         => 'shartnoma sanasi',
            'contract_file'         => 'shartnoma fayli',
            'supplier_id'           => 'yetkazib beruvchi',
            'bildirishnoma_number'  => 'bildirishnoma raqami',
            'bildirishnoma_file'    => 'bildirishnoma fayli',
            'ishonchnoma_number'    => 'ishonchnoma raqami',
            'ishonchnoma_date'      => 'ishonchnoma sanasi',
            'ishonchnoma_file'      => 'ishonchnoma fayli',
            'hisob_faktura'         => 'hisob-faktura raqami',
            'hisob_faktura_date'    => 'hisob-faktura sanasi',
            'hisob_faktura_file'    => 'hisob-faktura fayli',
            'items'                 => 'mahsulotlar',
            'items.*.product_name'  => ':position-mahsulot nomi',
            'items.*.unit_id'       => ":position-mahsulot o'lchov birligi",
            'items.*.quantity'      => ':position-mahsulot miqdori',
            'items.*.item_price'    => ':position-mahsulot narxi',
        ];
    }

    public function messages(): array
    {
        return [
            'required'        => ':attribute majburiy.',
            'string'          => ':attribute matn bo\'lishi kerak.',
            'date'            => ':attribute to\'g\'ri sana formatida bo\'lishi kerak.',
            'before_or_equal' => ':attribute bugungi kundan kelajakda bo\'lishi mumkin emas.',
            'numeric'         => ':attribute raqam bo\'lishi kerak.',
            'min.numeric'     => ':attribute :min dan kam bo\'lmasligi kerak.',
            'file'            => ':attribute fayl bo\'lishi kerak.',
            'mimes'           => ':attribute faqat PDF formatida bo\'lishi kerak.',
            'exists'          => 'Tanlangan :attribute mavjud emas.',
            'array'           => ':attribute ro\'yxat shaklida bo\'lishi kerak.',
            'min.array'       => 'Kamida bitta mahsulot kiritilishi shart.',
        ];
    }
}
