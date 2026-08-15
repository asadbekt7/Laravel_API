<?php
// app/Http/Requests/WarehouseBatch/CompleteBugalteriyaRequest.php

namespace App\Http\Requests\WarehouseBatch;

use App\Enums\ItemType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompleteBugalteriyaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'item_type' => ['required', Rule::enum(ItemType::class)],

            'expiry_date' => ['required_if:item_type,' . ItemType::ASOSIY_VOSITA->value, 'nullable', 'date'],
            'inventory_numbers' => ['required_if:item_type,' . ItemType::ASOSIY_VOSITA->value, 'nullable', 'array'],
            'inventory_numbers.*' => ['string', 'max:100'],

            'statya' => ['required_if:item_type,' . ItemType::RASXOD->value, 'nullable', 'string', 'max:255'],
        ];
    }
}
