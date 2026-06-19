<?php

namespace App\Http\Requests\Information;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInformationRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('information');

        return [
            'name'                    => 'sometimes|required|string|max:255|unique:informations,name,' . $id,
            'contract_number'         => 'sometimes|required|string|max:30',
            'contract_date'           => 'sometimes|required|date',
            'contract_file_path'      => 'sometimes|required|file|mimes:pdf,doc,docx|max:10240',
            'supplier_id'             => 'sometimes|required|exists:suppliers,id',
            'bildirishnoma_number'    => 'sometimes|required|string|max:30',
            'bildirishnoma_date'      => 'sometimes|required|date',
            'bildirishnoma_file_path' => 'sometimes|required|file|mimes:pdf,doc,docx|max:10240',
            'product_name'            => 'sometimes|required|string|max:255',
            'unit_id'                 => 'sometimes|required|exists:units,id',
            'quantity'                => 'sometimes|required|integer|min:1',
            'price'                   => 'sometimes|required|numeric|min:0',
            'ishonchnoma_number'      => 'sometimes|required|string|max:20',
            'ishonchnoma_date'        => 'sometimes|required|date',
            'ishonchnoma_file_path'   => 'sometimes|required|file|mimes:pdf,doc,docx|max:10240',
            'hisob_faktura'           => 'sometimes|required|string|max:30',
            'hisob_faktura_date'      => 'sometimes|required|date',
            'hisob_faktura_file_path' => 'sometimes|required|file|mimes:pdf,doc,docx|max:10240',
            'akt_number'              => 'sometimes|required|string|max:15',
            'akt_date'                => 'sometimes|required|date',
            'description'             => 'nullable|string',
        ];
    }
}
