<?php

namespace App\Http\Requests\Yetkazuvchi;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateYetkazuvchiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('yetkazuvchi');

        return [
            'name'       => ['sometimes', 'required', 'string', 'max:255'],
            'INN_number' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('yetkazuvchi', 'INN_number')->ignore($id),
            ],
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
