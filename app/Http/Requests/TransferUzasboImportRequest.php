<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferUzasboImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'import_ids'   => ['required', 'array', 'min:1'],
            'import_ids.*' => ['required', 'integer', 'exists:uzasbo_imports,id'],
            'type_id'      => ['required', 'integer', 'exists:types,id'],
            'category_id'  => ['required', 'integer', 'exists:categories,id'],
            'model_id'     => ['required', 'integer', 'exists:models,id'],
            'room_name'    => ['required', 'string', 'max:255'],
            'staff_id'     => ['required', 'integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'import_ids.required'  => 'import_ids maydoni talab qilinadi.',
            'import_ids.array'     => 'import_ids massiv bo\'lishi kerak.',
            'import_ids.min'       => 'import_ids kamida 1 ta element bo\'lishi kerak.',
            'import_ids.*.integer' => 'Har bir import_id butun son bo\'lishi kerak.',
            'import_ids.*.exists'  => ':input ID li uzasbo_imports yozuvi topilmadi.',
            'type_id.required'     => 'type_id maydoni talab qilinadi.',
            'type_id.exists'       => 'Tanlangan type_id tapes jadvalida mavjud emas.',
            'category_id.required' => 'category_id maydoni talab qilinadi.',
            'category_id.exists'   => 'Tanlangan category_id categories jadvalida mavjud emas.',
            'model_id.required'    => 'model_id maydoni talab qilinadi.',
            'model_id.exists'      => 'Tanlangan model_id models jadvalida mavjud emas.',
            'room_name.required'   => 'room_name maydoni talab qilinadi.',
            'staff_id.required'    => 'staff_id maydoni talab qilinadi.',
            'staff_id.integer'     => 'staff_id butun son bo\'lishi kerak.',
        ];
    }
}
