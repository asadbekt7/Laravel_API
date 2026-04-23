<?php

namespace App\Http\Requests\Computer;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateComputerRequest extends FormRequest
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
            'name'        => ['sometimes', 'string', 'max:255'],
            'category_id' => ['sometimes', 'integer', 'exists:categories,id'],
            'quantity'    => ['sometimes', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
            'room_name'   => ['nullable', 'string'],
            'building'    => ['nullable', 'string'],
            'room_number' => ['nullable', 'string'],
        ];
    }
}
