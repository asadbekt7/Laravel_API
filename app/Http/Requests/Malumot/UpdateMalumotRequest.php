<?php

namespace App\Http\Requests\Malumot;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMalumotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Kontrakt
            'contract_number'        => ['sometimes', 'required', 'string', 'max:255'],
            'contract_date'          => ['sometimes', 'required', 'date'],
            'contract_file_path'     => ['sometimes', 'required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],

            // Yetkazuvchi
            'yetkazuvchi_id'         => ['sometimes', 'required', 'integer', 'exists:yetkazuvchi,id'],

            // Bildirishnoma
            'bildirishnoma_number'   => ['sometimes', 'required', 'string', 'max:255'],
            'bildirishnoma_date'     => ['sometimes', 'required', 'date'],
            'bildirishnoma_file_path'=> ['sometimes', 'required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],

            // Mahsulot
            'name'                   => ['sometimes', 'required', 'string', 'max:255'],
            'unit_id'                => ['sometimes', 'required', 'integer', 'exists:units,id'],
            'quantity'               => ['sometimes', 'required', 'integer', 'min:1'],
            'price'                  => ['sometimes', 'required', 'numeric', 'min:0'],

            // Ishonchnoma
            'ishonchnoma_number'     => ['sometimes', 'required', 'string', 'max:255'],
            'ishonchnoma_date'       => ['sometimes', 'required', 'date'],
            'ishonchnoma_file_path'  => ['sometimes', 'required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],

            // Hisob-faktura
            'hisob_faktura'          => ['sometimes', 'required', 'string', 'max:255'],
            'hisob_faktura_date'     => ['sometimes', 'required', 'date'],
            'hisob_faktura_file_path'=> ['sometimes', 'required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],

            // Akt
            'akt_number'             => ['sometimes', 'required', 'string', 'max:255'],
            'akt_date'               => ['sometimes', 'required', 'date'],

            // Qo'shimcha
            'description'            => ['sometimes', 'required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            // Kontrakt
            'contract_number.required'         => 'Kontrakt raqami majburiy.',
            'contract_date.required'           => 'Kontrakt sanasi majburiy.',
            'contract_date.date'               => 'Kontrakt sanasi to\'g\'ri formatda bo\'lishi kerak.',
            'contract_file_path.required'      => 'Kontrakt fayli majburiy.',
            'contract_file_path.mimes'         => 'Kontrakt fayli pdf, jpg, jpeg yoki png formatida bo\'lishi kerak.',
            'contract_file_path.max'           => 'Kontrakt fayli 10MB dan oshmasligi kerak.',

            // Yetkazuvchi
            'yetkazuvchi_id.required'          => 'Yetkazuvchi majburiy.',
            'yetkazuvchi_id.exists'            => 'Tanlangan yetkazuvchi mavjud emas.',

            // Bildirishnoma
            'bildirishnoma_number.required'    => 'Bildirishnoma raqami majburiy.',
            'bildirishnoma_date.required'      => 'Bildirishnoma sanasi majburiy.',
            'bildirishnoma_date.date'          => 'Bildirishnoma sanasi to\'g\'ri formatda bo\'lishi kerak.',
            'bildirishnoma_file_path.required' => 'Bildirishnoma fayli majburiy.',
            'bildirishnoma_file_path.mimes'    => 'Bildirishnoma fayli pdf, jpg, jpeg yoki png formatida bo\'lishi kerak.',
            'bildirishnoma_file_path.max'      => 'Bildirishnoma fayli 10MB dan oshmasligi kerak.',

            // Mahsulot
            'name.required'                    => 'Mahsulot nomi majburiy.',
            'unit_id.required'                 => 'O\'lchov birligi majburiy.',
            'unit_id.exists'                   => 'Tanlangan o\'lchov birligi mavjud emas.',
            'quantity.required'                => 'Miqdor majburiy.',
            'quantity.integer'                 => 'Miqdor butun son bo\'lishi kerak.',
            'quantity.min'                     => 'Miqdor kamida 1 bo\'lishi kerak.',
            'price.required'                   => 'Narx majburiy.',
            'price.numeric'                    => 'Narx raqam bo\'lishi kerak.',
            'price.min'                        => 'Narx 0 dan kichik bo\'lmasligi kerak.',

            // Ishonchnoma
            'ishonchnoma_number.required'      => 'Ishonchnoma raqami majburiy.',
            'ishonchnoma_date.required'        => 'Ishonchnoma sanasi majburiy.',
            'ishonchnoma_date.date'            => 'Ishonchnoma sanasi to\'g\'ri formatda bo\'lishi kerak.',
            'ishonchnoma_file_path.required'   => 'Ishonchnoma fayli majburiy.',
            'ishonchnoma_file_path.mimes'      => 'Ishonchnoma fayli pdf, jpg, jpeg yoki png formatida bo\'lishi kerak.',
            'ishonchnoma_file_path.max'        => 'Ishonchnoma fayli 10MB dan oshmasligi kerak.',

            // Hisob-faktura
            'hisob_faktura.required'           => 'Hisob-faktura raqami majburiy.',
            'hisob_faktura_date.required'      => 'Hisob-faktura sanasi majburiy.',
            'hisob_faktura_date.date'          => 'Hisob-faktura sanasi to\'g\'ri formatda bo\'lishi kerak.',
            'hisob_faktura_file_path.required' => 'Hisob-faktura fayli majburiy.',
            'hisob_faktura_file_path.mimes'    => 'Hisob-faktura fayli pdf, jpg, jpeg yoki png formatida bo\'lishi kerak.',
            'hisob_faktura_file_path.max'      => 'Hisob-faktura fayli 10MB dan oshmasligi kerak.',

            // Akt
            'akt_number.required'              => 'Akt raqami majburiy.',
            'akt_date.required'                => 'Akt sanasi majburiy.',
            'akt_date.date'                    => 'Akt sanasi to\'g\'ri formatda bo\'lishi kerak.',

            // Qo'shimcha
            'description.required'             => 'Tavsif majburiy.',
        ];
    }
}
