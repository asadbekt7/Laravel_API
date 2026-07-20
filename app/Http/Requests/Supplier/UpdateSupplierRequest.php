<?php

namespace App\Http\Requests\Supplier;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'          => ['required', 'string', 'max:255', 'unique:suppliers,name,' . $this->route('id')],
            'INN_number'    => ['required', 'digits:9', 'unique:suppliers,INN_number,' . $this->route('id')],
            'JSHSHR_number' => ['required', 'digits:14', 'unique:suppliers,JSHSHR_number,' . $this->route('id')],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'          => 'Supplier nomi majburiy',
            'name.unique'            => 'Bu nom allaqachon mavjud',
            'name.max'               => 'Nom 255 ta belgidan oshmasligi kerak',
            'INN_number.required'    => 'INN raqami majburiy',
            'INN_number.digits'      => 'INN 9 ta raqamdan iborat bo\'lishi kerak',
            'INN_number.unique'      => 'Bu INN allaqachon mavjud',
            'JSHSHR_number.required' => 'JSHSHIR raqami majburiy',
            'JSHSHR_number.digits'   => 'JSHSHIR 14 ta raqamdan iborat bo\'lishi kerak',
            'JSHSHR_number.unique'   => 'Bu JSHSHIR allaqachon mavjud',
        ];
    }
}
