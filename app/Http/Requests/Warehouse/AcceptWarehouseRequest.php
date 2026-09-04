<?php

declare(strict_types=1);

namespace App\Http\Requests\Warehouse;

use App\Models\InformationModel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AcceptWarehouseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null; // aniq ruxsat 'perm' middleware'da tekshiriladi
    }

    public function rules(): array
    {
        /** @var InformationModel $information */
        $information = $this->route('information');
        $itemsCount  = $information?->items()->count() ?? 0;

        return [
            'akt_number'  => ['required', 'integer', 'min:1'],
            'akt_date'    => ['required', 'date', 'before_or_equal:today'],
            'location_id' => ['required', 'integer', Rule::exists('locations', 'id')],
            'description' => ['nullable', 'string'],

            // Items soni aynan information_items soniga teng bo'lishi shart -
            // hech bir mahsulot klassifikatsiyasiz qolmasligi kerak.
            'items' => ['required', 'array', "size:{$itemsCount}"],

            'items.*.information_item_id' => [
                'required', 'integer', 'distinct',
                Rule::exists('information_items', 'id')->where('information_id', $information?->id),
            ],
            'items.*.type_id'     => ['required', 'integer', Rule::exists('types', 'id')],
            'items.*.category_id' => ['nullable', 'integer', Rule::exists('categories', 'id')],
            'items.*.model_id'    => ['nullable', 'integer', Rule::exists('models', 'id')],
        ];
    }

    public function attributes(): array
    {
        return [
            'akt_number'                   => 'akt raqami',
            'akt_date'                     => 'akt sanasi',
            'location_id'                  => 'ombor manzili',
            'description'                  => 'izoh',
            'items'                        => 'mahsulotlar',
            'items.*.information_item_id'  => ':position-mahsulot',
            'items.*.type_id'              => ':position-mahsulot turi',
            'items.*.category_id'          => ':position-mahsulot kategoriyasi',
            'items.*.model_id'             => ':position-mahsulot modeli',
        ];
    }

    public function messages(): array
    {
        return [
            'required'   => ':attribute majburiy.',
            'items.size' => "Barcha mahsulotlar (jami :size ta) uchun klassifikatsiya kiritilishi shart.",
            'distinct'   => 'Bitta mahsulot faqat bir marta ko\'rsatilishi mumkin.',
            'exists'     => 'Tanlangan :attribute mavjud emas yoki shu ma\'lumotga tegishli emas.',
        ];
    }
}
