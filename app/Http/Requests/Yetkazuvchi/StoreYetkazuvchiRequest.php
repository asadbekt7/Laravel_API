<?php

namespace App\Http\Requests\Yetkazuvchi;

use Illuminate\Foundation\Http\FormRequest;

class StoreYetkazuvchiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'       => ['required', 'string', 'max:255'],
            'INN_number' => ['required', 'string', 'max:255', 'unique:yetkazuvchi,INN_number'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'       => 'Yetkazuvchi ismi majburiy.',
            'name.string'         => 'Yetkazuvchi ismi matn bo\'lishi kerak.',
            'name.max'            => 'Yetkazuvchi ismi 255 ta belgidan oshmasligi kerak.',
            'INN_number.required' => 'INN raqami majburiy.',
            'INN_number.string'   => 'INN raqami matn bo\'lishi kerak.',
            'INN_number.max'      => 'INN raqami 255 ta belgidan oshmasligi kerak.',
            'INN_number.unique'   => 'Bu INN raqami allaqachon mavjud.',
        ];
    }
}
