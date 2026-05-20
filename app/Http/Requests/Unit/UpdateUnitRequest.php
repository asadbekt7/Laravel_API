<?php

namespace App\Http\Requests\Unit;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('unit');

        return [
            'name' => ['required', 'string', 'max:255', "unique:units,name,{$id}"],
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
