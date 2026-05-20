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
            'building'     => ['required', 'string', 'max:255'],
            'room_number'  => ['required', 'string', 'max:50'],
            'lastName'     => ['required', 'string', 'max:255'],
            'firstName'    => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'import_ids.required'   => 'import_ids maydoni majburiy.',
            'import_ids.array'      => 'import_ids massiv bo\'lishi kerak.',
            'import_ids.min'        => 'import_ids kamida 1 ta element bo\'lishi kerak.',
            'import_ids.*.integer'  => 'import_ids elementlari butun son bo\'lishi kerak.',
            'import_ids.*.exists'   => 'Tanlangan import yozuvi mavjud emas.',
            'type_id.required'      => 'type_id maydoni majburiy.',
            'type_id.exists'        => 'Tanlangan type mavjud emas.',
            'category_id.required'  => 'category_id maydoni majburiy.',
            'category_id.exists'    => 'Tanlangan category mavjud emas.',
            'model_id.required'     => 'model_id maydoni majburiy.',
            'model_id.exists'       => 'Tanlangan model mavjud emas.',
            'room_name.required'    => 'room_name maydoni majburiy.',
            'building.required'     => 'building maydoni majburiy.',
            'room_number.required'  => 'room_number maydoni majburiy.',
            'lastName.required'     => 'lastName maydoni majburiy.',
            'firstName.required'    => 'firstName maydoni majburiy.',
        ];
    }
}
