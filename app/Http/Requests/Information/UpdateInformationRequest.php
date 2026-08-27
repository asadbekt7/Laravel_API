<?php

declare(strict_types=1);

namespace App\Http\Requests\Information;

use App\Models\InformationModel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInformationRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var InformationModel $information */
        $information = $this->route('information');

        return $this->user()?->can('update', $information) ?? false;
    }

    public function rules(): array
    {
        /** @var InformationModel $information */
        $information = $this->route('information');

        return [
            'name'        => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],

            'contract_number' => ['sometimes', 'required', 'string', 'max:255'],
            'contract_date'   => ['sometimes', 'required', 'date', 'before_or_equal:today'],
            'contract_file'   => ['nullable', 'file', 'mimes:pdf', 'max:5120'],

            'supplier_id' => ['sometimes', 'required', 'integer', Rule::exists('suppliers', 'id')],

            'bildirishnoma_number' => ['sometimes', 'required', 'string', 'max:255'],
            'bildirishnoma_date'   => ['nullable', 'date', 'before_or_equal:today'],
            'bildirishnoma_file'   => ['nullable', 'file', 'mimes:pdf', 'max:5120'],

            'ishonchnoma_number' => ['sometimes', 'required', 'string', 'max:255'],
            'ishonchnoma_date'   => ['sometimes', 'required', 'date', 'before_or_equal:today'],
            'ishonchnoma_file'   => ['nullable', 'file', 'mimes:pdf', 'max:5120'],

            'hisob_faktura'      => ['sometimes', 'required', 'string', 'max:255'],
            'hisob_faktura_date' => ['sometimes', 'required', 'date', 'before_or_equal:today'],
            'hisob_faktura_file' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],

            'items'                 => ['sometimes', 'array', 'min:1', 'max:200'],
            'items.*.id'            => [
                'nullable', 'integer',
                Rule::exists('information_items', 'id')->where('information_id', $information?->id),
            ],
            'items.*.product_name'  => ['required_with:items', 'string', 'max:255'],
            'items.*.unit_id'       => ['required_with:items', 'integer', Rule::exists('units', 'id')],
            'items.*.quantity'      => ['required_with:items', 'numeric', 'min:0.001'],
            'items.*.item_price'    => ['required_with:items', 'numeric', 'min:0', 'max:999999999999.99'],
        ];
    }
}
