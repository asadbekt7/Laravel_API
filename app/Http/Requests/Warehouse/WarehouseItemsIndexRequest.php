<?php

declare(strict_types=1);

namespace App\Http\Requests\Warehouse;

use Illuminate\Foundation\Http\FormRequest;

class WarehouseItemsIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null; // aniq ruxsat 'perm' middleware'da tekshiriladi
    }

    public function rules(): array
    {
        return [
            'warehouse_id'  => ['sometimes', 'integer', 'exists:warehouse,id'],
            'type_id'       => ['sometimes', 'integer', 'exists:types,id'],
            'category_id'   => ['sometimes', 'integer', 'exists:categories,id'],
            'model_id'      => ['sometimes', 'integer', 'exists:models,id'],
            'unit_id'       => ['sometimes', 'integer', 'exists:units,id'],
            'product_name'  => ['sometimes', 'string', 'max:255'],
            'akt_number'    => ['sometimes', 'string', 'max:255'],
            'price_from'    => ['sometimes', 'numeric', 'min:0'],
            'price_to'      => ['sometimes', 'numeric', 'min:0', 'gte:price_from'],
            'quantity_from' => ['sometimes', 'numeric', 'min:0'],
            'quantity_to'   => ['sometimes', 'numeric', 'min:0', 'gte:quantity_from'],
            'created_from'  => ['sometimes', 'date'],
            'created_to'    => ['sometimes', 'date', 'after_or_equal:created_from'],
            'sort_by'       => ['sometimes', 'string', 'in:id,created_at'],
            'sort_dir'      => ['sometimes', 'string', 'in:asc,desc'],
            'per_page'      => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }
}
