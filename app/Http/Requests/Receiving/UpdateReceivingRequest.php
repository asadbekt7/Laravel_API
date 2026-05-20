<?php

namespace App\Http\Requests\Receiving;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReceivingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('receiving');

        return [
            'document_type_id' => ['sometimes', 'integer', 'exists:document_types,id'],
            'document_number'  => ['sometimes', 'string', 'max:255', "unique:receivings,document_number,{$id}"],
            'document_date'    => ['sometimes', 'date'],
            'supplier_name'    => ['sometimes', 'string'],
            'delivery_date'    => ['sometimes', 'date'],
            'batch_number'     => ['sometimes', 'string', 'max:255'],
            'batch_cost'       => ['sometimes', 'numeric', 'min:0'],
            'notes'            => ['nullable', 'string'],
            'file_path'        => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'document_type_id.exists'  => 'Bunday document type mavjud emas.',
            'document_number.unique'   => 'Bu document number allaqachon mavjud.',
            'document_date.date'       => 'Document date sana formatida bolishi kerak.',
            'delivery_date.date'       => 'Delivery date sana formatida bolishi kerak.',
            'batch_cost.numeric'       => 'Batch cost son bolishi kerak.',
            'batch_cost.min'           => 'Batch cost manfiy bolishi mumkin emas.',
        ];
    }
}
