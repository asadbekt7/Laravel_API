<?php

declare(strict_types=1);

namespace App\Http\Requests\InformationItem;

use App\Models\InformationItem;
use App\Models\InformationModel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInformationItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var InformationModel $information */
        $information = $this->route('information');
        /** @var InformationItem $item */
        $item = $this->route('item');

        if ($item->information_id !== $information->id) {
            return false;
        }

        return $this->user()?->can('manageItems', $information) ?? false;
    }

    public function rules(): array
    {
        return [
            'product_name' => ['sometimes', 'required', 'string', 'max:255'],
            'unit_id'      => ['sometimes', 'required', 'integer', Rule::exists('units', 'id')],
            'quantity'     => ['sometimes', 'required', 'numeric', 'min:0.001'],
            'item_price'   => ['sometimes', 'required', 'numeric', 'min:0', 'max:999999999999.99'],
        ];
    }
}
