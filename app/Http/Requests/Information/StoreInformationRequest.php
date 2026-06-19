<?php

namespace App\Http\Requests\Information;

use Illuminate\Foundation\Http\FormRequest;

class StoreInformationRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'                    => 'required|string|max:255|unique:informations,name',
            'contract_number'         => 'required|string|max:30',
            'contract_date'           => 'required|date',
            'contract_file_path'      => 'required|file|mimes:pdf,doc,docx|max:10240',
            'supplier_id'             => 'required|exists:suppliers,id',
            'bildirishnoma_number'    => 'required|string|max:30',
            'bildirishnoma_date'      => 'required|date',
            'bildirishnoma_file_path' => 'required|file|mimes:pdf,doc,docx|max:10240',
            'product_name'            => 'required|string|max:255',
            'unit_id'                 => 'required|exists:units,id',
            'quantity'                => 'required|integer|min:1',
            'price'                   => 'required|numeric|min:0',
            'ishonchnoma_number'      => 'required|string|max:20',
            'ishonchnoma_date'        => 'required|date',
            'ishonchnoma_file_path'   => 'required|file|mimes:pdf,doc,docx|max:10240',
            'hisob_faktura'           => 'required|string|max:30',
            'hisob_faktura_date'      => 'required|date',
            'hisob_faktura_file_path' => 'required|file|mimes:pdf,doc,docx|max:10240',
            'akt_number'              => 'required|string|max:15',
            'akt_date'                => 'required|date',
            'description'             => 'nullable|string',
        ];
    }
}
