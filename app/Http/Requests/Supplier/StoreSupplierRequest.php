<?php

namespace App\Http\Requests\Supplier;

use Illuminate\Foundation\Http\FormRequest;

class StoreSupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:suppliers,name'],
            'INN_number' => ['required', 'integer', 'max:255', 'unique:suppliers,INN_number'],
            'JSHSHR_number' => ['required', 'integer', 'max:255', 'unique:suppliers,JSHSHR_number'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Supplier nomi majburiy',
            'name.unique'   => 'Bu nom allaqachon mavjud',
            'name.max'      => 'Nom 255 ta belgidan oshmasligi kerak',
        ];
    }
}
