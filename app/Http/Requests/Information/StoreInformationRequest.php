<?php

namespace App\Http\Requests\Information;

use Illuminate\Foundation\Http\FormRequest;

class StoreInformationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                       => ['required', 'string', 'max:255'],
            'contract_number'            => ['required', 'string', 'max:255'],
            'contract_date'              => ['required', 'date'],
            'contract_file_path'         => ['required', 'file', 'mimes:pdf', 'max:5120'],
            'contract_file_name'         => ['required', 'string', 'max:255'],
            'supplier_id'                => ['required', 'integer', 'exists:suppliers,id'],
            'bildirishnoma_number'       => ['required', 'string', 'max:255'],
            'bildirishnoma_date'         => ['nullable', 'date'],
            'bildirishnoma_file_path'    => ['required', 'file', 'mimes:pdf', 'max:5120'],
            'bildirishnoma_file_name'    => ['required', 'string', 'max:255'],
            'product_name'               => ['required', 'string', 'max:255'],
            'unit_id'                    => ['required', 'integer', 'exists:units,id'],
            'quantity'                   => ['required', 'integer', 'min:1'],
            'price'                      => ['required', 'numeric', 'min:0'],
            'ishonchnoma_number'         => ['required', 'string', 'max:255'],
            'ishonchnoma_date'           => ['required', 'date'],
            'ishonchnoma_file_path'      => ['required', 'file', 'mimes:pdf', 'max:5120'],
            'ishonchnoma_file_name'      => ['required', 'string', 'max:255'],
            'hisob_faktura'              => ['required', 'string', 'max:255'],
            'hisob_faktura_date'         => ['required', 'date'],
            'hisob_faktura_file_path'    => ['required', 'file', 'mimes:pdf', 'max:5120'],
            'hisob_faktura_file_name'    => ['required', 'string', 'max:255'],
            'akt_number'                 => ['required', 'string', 'max:255'],
            'akt_date'                   => ['required', 'date'],
            'description'                => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'            => 'Nomi majburiy.',
            'name.max'                 => 'Nomi 255 belgidan oshmasligi kerak.',
            'contract_number.required' => 'Shartnoma raqami majburiy.',
            'contract_date.required'   => 'Shartnoma sanasi majburiy.',
            'contract_date.date'       => 'Shartnoma sanasi to\'g\'ri formatda bo\'lishi kerak.',
            'supplier_id.required'     => 'Yetkazib beruvchi majburiy.',
            'supplier_id.exists'       => 'Tanlangan yetkazib beruvchi mavjud emas.',
            'product_name.required'    => 'Mahsulot nomi majburiy.',
            'unit_id.required'         => 'O\'lchov birligi majburiy.',
            'unit_id.exists'           => 'Tanlangan o\'lchov birligi mavjud emas.',
            'quantity.required'        => 'Miqdor majburiy.',
            'quantity.integer'         => 'Miqdor butun son bo\'lishi kerak.',
            'quantity.min'             => 'Miqdor 1 dan kam bo\'lmasligi kerak.',
            'price.required'           => 'Narx majburiy.',
            'price.numeric'            => 'Narx raqam bo\'lishi kerak.',
            'price.min'                => 'Narx 0 dan kam bo\'lmasligi kerak.',
        ];
    }
}
