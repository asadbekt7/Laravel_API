<?php
// app/Http/Requests/WarehouseStoreRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WarehouseStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'          => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'model_id'     => ['required', 'integer', 'exists:models,id'],
            'unit_id'      => ['required', 'integer', 'exists:units,id'],
            'quantity'      => ['required', 'integer', 'min:1', 'max:1000'],
            'description'   => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'          => 'Nom kiritilishi shart',
            'category_id.required' => 'Kategoriya tanlanishi shart',
            'category_id.exists'   => 'Kategoriya topilmadi',
            'model_id.required'     => 'Model tanlanishi shart',
            'model_id.exists'       => 'Model topilmadi',
            'unit_id.required'      => "O'lchov birligi tanlanishi shart",
            'unit_id.exists'        => "O'lchov birligi topilmadi",
            'quantity.min'           => 'Miqdor kamida 1 bo\'lishi kerak',
            'quantity.max'           => 'Miqdor 1000 dan oshmasligi kerak',
        ];
    }
}
