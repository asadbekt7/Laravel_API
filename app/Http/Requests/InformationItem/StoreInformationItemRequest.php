<?php

declare(strict_types=1);

namespace App\Http\Requests\InformationItem;

use App\Models\InformationModel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInformationItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var InformationModel $information */
        $information = $this->route('information');

        return $this->user()?->can('manageItems', $information) ?? false;
    }

    public function rules(): array
    {
        return [
            'product_name' => ['required', 'string', 'max:255'],
            'unit_id'      => ['required', 'integer', Rule::exists('units', 'id')],
            'quantity'     => ['required', 'numeric', 'min:0.001'],
            'item_price'   => ['required', 'numeric', 'min:0', 'max:999999999999.99'],
        ];
    }
}
