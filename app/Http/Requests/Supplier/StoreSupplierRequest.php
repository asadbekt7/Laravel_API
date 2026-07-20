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
            'name'          => ['required', 'string', 'max:255', 'unique:suppliers,name'],
            'INN_number'    => ['nullable', 'digits:9', 'unique:suppliers,INN_number'],
            'JSHSHR_number' => ['nullable', 'digits:14', 'unique:suppliers,JSHSHR_number'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'        => 'Supplier nomi majburiy',
            'name.unique'          => 'Bu nom allaqachon mavjud',
            'name.max'             => 'Nom 255 ta belgidan oshmasligi kerak',
            'INN_number.digits'    => 'INN 9 ta raqamdan iborat bo\'lishi kerak',
            'INN_number.unique'    => 'Bu INN allaqachon mavjud',
            'JSHSHR_number.digits' => 'JSHSHIR 14 ta raqamdan iborat bo\'lishi kerak',
            'JSHSHR_number.unique' => 'Bu JSHSHIR allaqachon mavjud',
        ];
    }
}
