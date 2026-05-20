<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'    => ['required', 'string', 'max:255', 'unique:categories,name'],
            'type_id' => ['required', 'integer', 'exists:types,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'    => 'Name maydoni majburiy.',
            'name.unique'      => 'Bu nom allaqachon mavjud.',
            'name.max'         => 'Name 255 ta belgidan oshmasligi kerak.',
            'type_id.required' => 'Type maydoni majburiy.',
            'type_id.integer'  => 'Type id butun son bolishi kerak.',
            'type_id.exists'   => 'Bunday type mavjud emas.',
        ];
    }
}
