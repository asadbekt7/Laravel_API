<?php

namespace App\Http\Requests\Items;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateItemStaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Kiruvchi ma'lumotlarni tozalash (trim).
     */
    protected function prepareForValidation(): void
    {
        $fields = ['room_name', 'last_name', 'first_name', 'middle_name'];

        $clean = [];
        foreach ($fields as $field) {
            if ($this->has($field)) {
                $clean[$field] = is_string($this->input($field))
                    ? trim($this->input($field))
                    : $this->input($field);
            }
        }

        $this->merge($clean);
    }

    public function rules(): array
    {
        return [
            // item_ids — bulk update uchun (ixtiyoriy, berilmasa route {id} ishlatiladi)
            'item_ids'    => ['sometimes', 'array', 'min:1'],
            'item_ids.*'  => ['integer', 'min:1'],

            // Xona (ixtiyoriy — faqat o'zgartirmoqchi bo'lsa)
            'room_name'   => ['sometimes', 'nullable', 'string', 'max:100'],

            // Xodim (ixtiyoriy — faqat o'zgartirmoqchi bo'lsa)
            'last_name'   => ['sometimes', 'nullable', 'string', 'max:100'],
            'first_name'  => ['sometimes', 'nullable', 'string', 'max:100'],
            'middle_name' => ['sometimes', 'nullable', 'string', 'max:100'],
            'staff_id'    => ['sometimes', 'nullable', 'integer', 'min:1'],
        ];
    }

    /**
     * Validatsiyadan keyin qo'shimcha mantiqiy tekshiruv:
     * kamida xona yoki xodim ma'lumoti bo'lishi shart.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            $hasRoom  = filled($this->input('room_name'));
            $hasStaff = filled($this->input('last_name')) || filled($this->input('staff_id'));

            if (! $hasRoom && ! $hasStaff) {
                $v->errors()->add(
                    'general',
                    'Kamida xona (room_name) yoki xodim (last_name / staff_id) ma\'lumoti berilishi shart.'
                );
            }

            // staff_id berilsa last_name ham majburiy
            if (filled($this->input('staff_id')) && ! filled($this->input('last_name'))) {
                $v->errors()->add(
                    'last_name',
                    'staff_id bilan birga last_name ham berilishi shart.'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'item_ids.array'    => 'item_ids massiv bo\'lishi shart.',
            'item_ids.min'      => 'Kamida bitta item_id berilishi shart.',
            'item_ids.*.integer'=> 'Har bir item_id butun son bo\'lishi shart.',
            'item_ids.*.min'    => 'item_id musbat son bo\'lishi shart.',

            'room_name.string'  => 'Xona nomi matn bo\'lishi shart.',
            'room_name.max'     => 'Xona nomi 100 belgidan oshmasligi shart.',

            'last_name.string'  => 'Familiya matn bo\'lishi shart.',
            'last_name.max'     => 'Familiya 100 belgidan oshmasligi shart.',
            'first_name.string' => 'Ism matn bo\'lishi shart.',
            'first_name.max'    => 'Ism 100 belgidan oshmasligi shart.',
            'middle_name.string'=> 'Otasining ismi matn bo\'lishi shart.',
            'middle_name.max'   => 'Otasining ismi 100 belgidan oshmasligi shart.',
            'staff_id.integer'  => 'Xodim ID butun son bo\'lishi shart.',
            'staff_id.min'      => 'Xodim ID musbat son bo\'lishi shart.',
        ];
    }

    // -------------------------------------------------------------------------
    // Qulay accessor metodlar (Controller da $request->params() ishlatish uchun)
    // -------------------------------------------------------------------------

    /**
     * EditItemStaffService::edit() ga uzatiladigan params massivini qaytaradi.
     */
    public function params(): array
    {
        return array_filter([
            'room_name'   => $this->input('room_name'),
            'last_name'   => $this->input('last_name'),
            'first_name'  => $this->input('first_name'),
            'middle_name' => $this->input('middle_name'),
            'staff_id'    => $this->input('staff_id'),
        ], fn($value) => ! is_null($value) && $value !== '');
    }

    /**
     * Bulk update uchun item ID lar (agar berilgan bo'lsa).
     *
     * @return int[]
     */
    public function itemIds(): array
    {
        return $this->input('item_ids', []);
    }

    /**
     * Validatsiya xatolari JSON formatida qaytariladi.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validatsiya xatosi.',
                'errors'  => $validator->errors(),
            ], 422)
        );
    }
}
