<?php

namespace App\Http\Requests\Information;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInformationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                       => ['sometimes', 'required', 'string', 'max:255'],
            'contract_number'            => ['sometimes', 'required', 'string', 'max:255'],
            'contract_date'              => ['sometimes', 'required', 'date'],
            'contract_file_path'         => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
            'contract_file_name'         => ['nullable', 'string', 'max:255'],
            'supplier_id'                => ['sometimes', 'required', 'integer', 'exists:suppliers,id'],
            'bildirishnoma_number'       => ['sometimes', 'required', 'string', 'max:255'],
            'bildirishnoma_date'         => ['nullable', 'date'],
            'bildirishnoma_file_path'    => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
            'bildirishnoma_file_name'    => ['nullable', 'string', 'max:255'],
            'product_name'               => ['sometimes', 'required', 'string', 'max:255'],
            'unit_id'                    => ['sometimes', 'required', 'integer', 'exists:units,id'],
            'quantity'                   => ['sometimes', 'required', 'integer', 'min:1'],
            'price'                      => ['sometimes', 'required', 'numeric', 'min:0'],
            'ishonchnoma_number'         => ['sometimes', 'required', 'string', 'max:255'],
            'ishonchnoma_date'           => ['sometimes', 'required', 'date'],
            'ishonchnoma_file_path'      => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
            'ishonchnoma_file_name'      => ['nullable', 'string', 'max:255'],
            'hisob_faktura'              => ['sometimes', 'required', 'string', 'max:255'],
            'hisob_faktura_date'         => ['sometimes', 'required', 'date'],
            'hisob_faktura_file_path'    => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
            'hisob_faktura_file_name'    => ['nullable', 'string', 'max:255'],
            'akt_number'                 => ['sometimes', 'required', 'string', 'max:255'],
            'akt_date'                   => ['sometimes', 'required', 'date'],
            'description'                => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'               => 'Nomi majburiy.',
            'name.max'                    => 'Nomi 255 belgidan oshmasligi kerak.',
            'contract_number.required'    => 'Shartnoma raqami majburiy.',
            'contract_date.required'      => 'Shartnoma sanasi majburiy.',
            'contract_date.date'          => 'Shartnoma sanasi to\'g\'ri formatda bo\'lishi kerak.',
            'contract_file_path.mimes'    => 'Shartnoma fayli faqat PDF formatida bo\'lishi kerak.',
            'contract_file_path.max'      => 'Shartnoma fayli 5MB dan oshmasligi kerak.',
            'supplier_id.required'        => 'Yetkazib beruvchi majburiy.',
            'supplier_id.exists'          => 'Tanlangan yetkazib beruvchi mavjud emas.',
            'bildirishnoma_number.required' => 'Bildirishnoma raqami majburiy.',
            'bildirishnoma_file_path.mimes' => 'Bildirishnoma fayli faqat PDF formatida bo\'lishi kerak.',
            'bildirishnoma_file_path.max'   => 'Bildirishnoma fayli 5MB dan oshmasligi kerak.',
            'product_name.required'       => 'Mahsulot nomi majburiy.',
            'unit_id.required'            => 'O\'lchov birligi majburiy.',
            'unit_id.exists'              => 'Tanlangan o\'lchov birligi mavjud emas.',
            'quantity.required'           => 'Miqdor majburiy.',
            'quantity.integer'            => 'Miqdor butun son bo\'lishi kerak.',
            'quantity.min'                => 'Miqdor 1 dan kam bo\'lmasligi kerak.',
            'price.required'              => 'Narx majburiy.',
            'price.numeric'               => 'Narx raqam bo\'lishi kerak.',
            'price.min'                   => 'Narx 0 dan kam bo\'lmasligi kerak.',
            'ishonchnoma_number.required' => 'Ishonchnoma raqami majburiy.',
            'ishonchnoma_date.required'   => 'Ishonchnoma sanasi majburiy.',
            'ishonchnoma_file_path.mimes' => 'Ishonchnoma fayli faqat PDF formatida bo\'lishi kerak.',
            'ishonchnoma_file_path.max'   => 'Ishonchnoma fayli 5MB dan oshmasligi kerak.',
            'hisob_faktura.required'      => 'Hisob faktura raqami majburiy.',
            'hisob_faktura_date.required' => 'Hisob faktura sanasi majburiy.',
            'hisob_faktura_file_path.mimes' => 'Hisob faktura fayli faqat PDF formatida bo\'lishi kerak.',
            'hisob_faktura_file_path.max'   => 'Hisob faktura fayli 5MB dan oshmasligi kerak.',
            'akt_number.required'         => 'Akt raqami majburiy.',
            'akt_date.required'           => 'Akt sanasi majburiy.',
        ];
    }
}
