<?php

namespace App\Http\Requests\Computer;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreComputerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'model_id'    => ['required', 'integer', 'exists:models,id'],
            'unit_id'     => ['required', 'integer', 'exists:units,id'],
            'quantity'    => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
        ];
    }
}
