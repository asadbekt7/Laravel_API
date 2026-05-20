<?php

namespace App\Http\Requests\Receiving;

use Illuminate\Foundation\Http\FormRequest;

class StoreReceivingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'document_type_id' => ['required', 'integer', 'exists:document_types,id'],
            'document_number'  => ['required', 'string', 'max:255', 'unique:receivings,document_number'],
            'document_date'    => ['required', 'date'],
            'supplier_name'    => ['required', 'string'],
            'delivery_date'    => ['required', 'date'],
            'batch_number'     => ['required', 'string', 'max:255'],
            'batch_cost'       => ['sometimes', 'numeric', 'min:0'],
            'notes'            => ['nullable', 'string'],
            'file_path'        => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'document_type_id.required' => 'Document type maydoni majburiy.',
            'document_type_id.exists'   => 'Bunday document type mavjud emas.',
            'document_number.required'  => 'Document number maydoni majburiy.',
            'document_number.unique'    => 'Bu document number allaqachon mavjud.',
            'document_date.required'    => 'Document date maydoni majburiy.',
            'document_date.date'        => 'Document date sana formatida bolishi kerak.',
            'supplier_name.required'    => 'Supplier name maydoni majburiy.',
            'delivery_date.required'    => 'Delivery date maydoni majburiy.',
            'delivery_date.date'        => 'Delivery date sana formatida bolishi kerak.',
            'batch_number.required'     => 'Batch number maydoni majburiy.',
            'batch_cost.numeric'        => 'Batch cost son bolishi kerak.',
            'batch_cost.min'            => 'Batch cost manfiy bolishi mumkin emas.',
        ];
    }
}
