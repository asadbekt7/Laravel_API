<?php

namespace App\Http\Requests\Model;

use Illuminate\Foundation\Http\FormRequest;

class StoreModelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'        => 'Name maydoni majburiy.',
            'name.max'             => 'Name 255 ta belgidan oshmasligi kerak.',
            'category_id.required' => 'Category maydoni majburiy.',
            'category_id.integer'  => 'Category id butun son bolishi kerak.',
            'category_id.exists'   => 'Bunday category mavjud emas.',
        ];
    }
}
