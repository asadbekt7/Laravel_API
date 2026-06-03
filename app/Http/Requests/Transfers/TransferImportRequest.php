<?php

namespace App\Http\Requests\Transfers;

use Illuminate\Foundation\Http\FormRequest;

class TransferImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'import_ids'   => ['required', 'array', 'min:1'],
            // FIX #6 — duplicate ID larni validatsiyada ham to'sish
            'import_ids.*' => ['required', 'integer', 'exists:uzasbo_imports,id', 'distinct'],
            'type_id'      => ['required', 'integer', 'exists:types,id'],
            'category_id'  => ['required', 'integer', 'exists:categories,id'],
            'model_id'     => ['required', 'integer', 'exists:models,id'],
            'room_name'    => ['required', 'string', 'min:1'],
            'last_name'    => ['required', 'string', 'min:2'],
            'staff_id'     => ['nullable', 'string', 'max:50'],
            'first_name'   => ['nullable', 'string', 'max:100'],
            'middle_name'  => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'import_ids.required'    => 'import_ids maydoni talab qilinadi.',
            'import_ids.array'       => 'import_ids array bo\'lishi kerak.',
            'import_ids.*.exists'    => ':input ID li import topilmadi.',
            'import_ids.*.distinct'  => 'import_ids da takroriy ID lar bo\'lmasligi kerak.',
            'type_id.required'       => 'type_id maydoni talab qilinadi.',
            'type_id.exists'         => 'Tanlangan type topilmadi.',
            'category_id.required'   => 'category_id maydoni talab qilinadi.',
            'category_id.exists'     => 'Tanlangan category topilmadi.',
            'model_id.required'      => 'model_id maydoni talab qilinadi.',
            'model_id.exists'        => 'Tanlangan model topilmadi.',
            'room_name.required'     => 'room_name maydoni talab qilinadi.',
            'last_name.required'     => 'last_name maydoni talab qilinadi.',
            'last_name.min'          => 'last_name kamida 2 ta belgi bo\'lishi kerak.',
        ];
    }
}
