<?php

namespace App\Http\Requests\Information;

use App\Enums\InformationStatus;
use Illuminate\Foundation\Http\FormRequest;

class UpdateInformationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $information = $this->route('information');

        // Faqat yaratgan odam va faqat "pending" holatda tahrirlashi mumkin
        return $information->creator_id === $this->user()?->id
            && $information->status === InformationStatus::Pending;
    }

    public function rules(): array
    {
        return [
            'name'        => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],

            // Shartnoma
            'contract_number' => ['sometimes', 'required', 'string', 'max:255'],
            'contract_date'   => ['sometimes', 'required', 'date', 'before_or_equal:today'],
            'contract_file'   => ['nullable', 'file', 'mimes:pdf', 'max:5120'],

            // Yetkazib beruvchi
            'supplier_id' => ['sometimes', 'required', 'integer', 'exists:suppliers,id'],

            // Bildirishnoma
            'bildirishnoma_number' => ['sometimes', 'required', 'string', 'max:255'],
            'bildirishnoma_date'   => ['nullable', 'date', 'before_or_equal:today'],
            'bildirishnoma_file'   => ['nullable', 'file', 'mimes:pdf', 'max:5120'],

            // Mahsulot
            'product_name' => ['sometimes', 'required', 'string', 'max:255'],
            'unit_id'      => ['sometimes', 'required', 'integer', 'exists:units,id'],
            'quantity'     => ['sometimes', 'required', 'integer', 'min:1'],
            'price'        => ['sometimes', 'required', 'numeric', 'min:0', 'max:999999999999.99'],

            // Ishonchnoma
            'ishonchnoma_number' => ['sometimes', 'required', 'string', 'max:255'],
            'ishonchnoma_date'   => ['sometimes', 'required', 'date', 'before_or_equal:today'],
            'ishonchnoma_file'   => ['nullable', 'file', 'mimes:pdf', 'max:5120'],

            // Hisob-faktura
            'hisob_faktura'      => ['sometimes', 'required', 'string', 'max:255'],
            'hisob_faktura_date' => ['sometimes', 'required', 'date', 'before_or_equal:today'],
            'hisob_faktura_file' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],

            // Akt
            'akt_number' => ['sometimes', 'required', 'string', 'max:255'],
            'akt_date'   => ['sometimes', 'required', 'date', 'before_or_equal:today'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name'                 => 'nomi',
            'description'          => 'izoh',
            'contract_number'      => 'shartnoma raqami',
            'contract_date'        => 'shartnoma sanasi',
            'contract_file'        => 'shartnoma fayli',
            'supplier_id'          => 'yetkazib beruvchi',
            'bildirishnoma_number' => 'bildirishnoma raqami',
            'bildirishnoma_date'   => 'bildirishnoma sanasi',
            'bildirishnoma_file'   => 'bildirishnoma fayli',
            'product_name'         => 'mahsulot nomi',
            'unit_id'              => "o'lchov birligi",
            'quantity'             => 'miqdor',
            'price'                => 'narx',
            'ishonchnoma_number'   => 'ishonchnoma raqami',
            'ishonchnoma_date'     => 'ishonchnoma sanasi',
            'ishonchnoma_file'     => 'ishonchnoma fayli',
            'hisob_faktura'        => 'hisob-faktura raqami',
            'hisob_faktura_date'   => 'hisob-faktura sanasi',
            'hisob_faktura_file'   => 'hisob-faktura fayli',
            'akt_number'           => 'akt raqami',
            'akt_date'             => 'akt sanasi',
        ];
    }

    public function messages(): array
    {
        return [
            'required'        => ':attribute majburiy.',
            'string'          => ':attribute matn bo\'lishi kerak.',
            'max.string'      => ':attribute :max belgidan oshmasligi kerak.',
            'max.file'        => ':attribute hajmi :max KB dan oshmasligi kerak.',
            'date'            => ':attribute to\'g\'ri sana formatida bo\'lishi kerak.',
            'before_or_equal' => ':attribute bugungi kundan kelajakda bo\'lishi mumkin emas.',
            'integer'         => ':attribute butun son bo\'lishi kerak.',
            'numeric'         => ':attribute raqam bo\'lishi kerak.',
            'min.numeric'     => ':attribute :min dan kam bo\'lmasligi kerak.',
            'file'            => ':attribute fayl bo\'lishi kerak.',
            'mimes'           => ':attribute faqat PDF formatida bo\'lishi kerak.',
            'exists'          => 'Tanlangan :attribute mavjud emas.',
        ];
    }
}
