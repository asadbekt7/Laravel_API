<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateComputerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => ['sometimes', 'required', 'string', 'max:255'],
            'category_id' => ['sometimes', 'required', 'integer', 'exists:categories,id'],
            'model_id'    => ['sometimes', 'required', 'integer', 'exists:models,id'],
            'unit_id'     => ['sometimes', 'required', 'integer', 'exists:units,id'],
            'quantity'    => ['sometimes', 'required', 'integer', 'min:1'],
            'description' => ['sometimes', 'required', 'string'],
            'room_name'   => ['sometimes', 'required', 'string', 'max:255'],
            'room_number' => ['sometimes', 'required', 'string', 'max:100'],
            'building'    => ['sometimes', 'required', 'string', 'max:255'],
            'employee_id' => ['nullable', 'integer'],
        ];
    }
}
