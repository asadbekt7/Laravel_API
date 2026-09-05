<?php

declare(strict_types=1);

namespace App\Http\Requests\Tmz;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateTmzRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // 'sometimes' — PATCH so'rovida faqat yuborilgan maydon tekshiriladi
            'name' => ['sometimes', 'required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nomi majburiy maydon.',
            'name.max' => 'Nomi 255 belgidan oshmasligi kerak.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validatsiya xatoligi.',
            'errors' => $validator->errors(),
        ], 422));
    }
}
