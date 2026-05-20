<?php

namespace App\Http\Requests\Location;

use Illuminate\Foundation\Http\FormRequest;

class StoreLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:locations,name'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name maydoni majburiy.',
            'name.unique'   => 'Bu nom allaqachon mavjud.',
            'name.max'      => 'Name 255 ta belgidan oshmasligi kerak.',
        ];
    }
}
