<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InventoryImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file'        => ['required', 'file', 'mimes:xlsx,csv', 'max:5120'],
            'import_type' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'Fayl yuklanishi majburiy.',
            'file.file'     => 'Yuklangan element fayl bo\'lishi kerak.',
            'file.mimes'    => 'Faqat .xlsx va .csv formatdagi fayllar qabul qilinadi.',
            'file.max'      => 'Fayl hajmi 5MB dan oshmasligi kerak.',
        ];
    }
}
