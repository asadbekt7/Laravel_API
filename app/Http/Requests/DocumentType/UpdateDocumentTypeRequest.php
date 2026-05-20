<?php

namespace App\Http\Requests\DocumentType;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDocumentTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('document_type');

        return [
            'name' => ['required', 'string', 'max:255', "unique:document_types,name,{$id}"],
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
