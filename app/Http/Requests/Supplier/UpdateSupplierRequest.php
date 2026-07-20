<?php

namespace App\Http\Requests\Supplier;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Route parametri nomiga qarab: {supplier} bo'lsa 'supplier', {id} bo'lsa 'id'
        $supplierId = $this->route('supplier') ?? $this->route('id');

        return [
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('suppliers', 'name')->ignore($supplierId),
            ],
            'INN_number' => [
                'nullable', 'digits:9',
                Rule::unique('suppliers', 'INN_number')->ignore($supplierId),
            ],
            'JSHSHR_number' => [
                'nullable', 'digits:14',
                Rule::unique('suppliers', 'JSHSHR_number')->ignore($supplierId),
            ],
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
