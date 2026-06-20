<?php

namespace App\Http\Requests\Information;

use Illuminate\Foundation\Http\FormRequest;

class BulkInformationRequest extends FormRequest
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

            // Mahsulotlar ro'yxati (array)
            'items'                      => ['required', 'array', 'min:1'],
            'items.*.product_name'       => ['required', 'string', 'max:255'],
            'items.*.unit_id'            => ['required', 'integer', 'exists:units,id'],
            'items.*.quantity'           => ['required', 'integer', 'min:1'],
            'items.*.price'              => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'                    => 'Nomi majburiy.',
            'contract_number.required'         => 'Shartnoma raqami majburiy.',
            'contract_date.required'           => 'Shartnoma sanasi majburiy.',
            'contract_file_path.required'      => 'Shartnoma fayli majburiy.',
            'contract_file_path.mimes'         => 'Shartnoma fayli faqat PDF formatida bo\'lishi kerak.',
            'contract_file_path.max'           => 'Shartnoma fayli 5MB dan oshmasligi kerak.',
            'supplier_id.required'             => 'Yetkazib beruvchi majburiy.',
            'supplier_id.exists'               => 'Tanlangan yetkazib beruvchi mavjud emas.',
            'bildirishnoma_number.required'    => 'Bildirishnoma raqami majburiy.',
            'bildirishnoma_file_path.required' => 'Bildirishnoma fayli majburiy.',
            'bildirishnoma_file_path.mimes'    => 'Bildirishnoma fayli faqat PDF formatida bo\'lishi kerak.',
            'bildirishnoma_file_path.max'      => 'Bildirishnoma fayli 5MB dan oshmasligi kerak.',
            'ishonchnoma_number.required'      => 'Ishonchnoma raqami majburiy.',
            'ishonchnoma_date.required'        => 'Ishonchnoma sanasi majburiy.',
            'ishonchnoma_file_path.required'   => 'Ishonchnoma fayli majburiy.',
            'ishonchnoma_file_path.mimes'      => 'Ishonchnoma fayli faqat PDF formatida bo\'lishi kerak.',
            'ishonchnoma_file_path.max'        => 'Ishonchnoma fayli 5MB dan oshmasligi kerak.',
            'hisob_faktura.required'           => 'Hisob faktura raqami majburiy.',
            'hisob_faktura_date.required'      => 'Hisob faktura sanasi majburiy.',
            'hisob_faktura_file_path.required' => 'Hisob faktura fayli majburiy.',
            'hisob_faktura_file_path.mimes'    => 'Hisob faktura fayli faqat PDF formatida bo\'lishi kerak.',
            'hisob_faktura_file_path.max'      => 'Hisob faktura fayli 5MB dan oshmasligi kerak.',
            'akt_number.required'              => 'Akt raqami majburiy.',
            'akt_date.required'                => 'Akt sanasi majburiy.',

            // Items xabarlari
            'items.required'                   => 'Kamida bitta mahsulot kiritilishi shart.',
            'items.array'                      => 'Mahsulotlar ro\'yxat shaklida bo\'lishi kerak.',
            'items.min'                        => 'Kamida bitta mahsulot kiritilishi shart.',
            'items.*.product_name.required'    => ':position-mahsulot nomi majburiy.',
            'items.*.unit_id.required'         => ':position-mahsulot o\'lchov birligi majburiy.',
            'items.*.unit_id.exists'           => ':position-mahsulot o\'lchov birligi mavjud emas.',
            'items.*.quantity.required'        => ':position-mahsulot miqdori majburiy.',
            'items.*.quantity.integer'         => ':position-mahsulot miqdori butun son bo\'lishi kerak.',
            'items.*.quantity.min'             => ':position-mahsulot miqdori 1 dan kam bo\'lmasligi kerak.',
            'items.*.price.required'           => ':position-mahsulot narxi majburiy.',
            'items.*.price.numeric'            => ':position-mahsulot narxi raqam bo\'lishi kerak.',
            'items.*.price.min'                => ':position-mahsulot narxi 0 dan kam bo\'lmasligi kerak.',
        ];
    }
}
