<?php

namespace App\Http\Requests\Information;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInformationRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'akt_raqam'                    => 'nullable|string|max:15',
            'akt_sana'                     => 'nullable|date',
            'schet_faktura_raqam'          => 'nullable|string|max:15',
            'schet_faktura_sana'           => 'nullable|date',
            'supplier_id'                  => 'sometimes|required|exists:suppliers,id',
            'shartnoma_raqam'              => 'nullable|string|max:30',
            'shartnoma_sana'               => 'nullable|date',
            'shartnoma_fayl'               => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'xaridor_full_name'            => 'nullable|string|max:50',
            'ishonchnoma_raqam'            => 'nullable|string|max:20',
            'ishonchnoma_sana'             => 'nullable|date',
            'ishonchnoma_fayl'             => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'location_id'                  => 'sometimes|required|exists:locations,id',
            'ombor_qabul_sana'             => 'nullable|date',
            'ombor_qabul_xodim_full_name'  => 'nullable|string',
            'jami_summa'                   => 'nullable|string',
        ];
    }
}
