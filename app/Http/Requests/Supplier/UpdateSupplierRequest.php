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
            'name' => ['required', 'string', 'max:255', 'unique:suppliers,name,' . $this->route('id')],
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
