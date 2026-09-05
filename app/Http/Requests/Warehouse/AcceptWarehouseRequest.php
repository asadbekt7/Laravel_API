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

        $rules = [
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

            // YANGI: aktiv turi. Qolgan maydonlarning barchasi (type_id,
            // category_id, model_id, tmz_id) aynan shu qiymatga qarab
            // shartli tekshiriladi (pastdagi foreach ichida).
            'items.*.asset_type' => ['required', Rule::in(['asosiy', 'tmz'])],

            // responsible_person_id mahalliy jadvalga emas, tashqi StaffAPI'ga
            // tegishli (StaffApiService::searchStaff natijasidagi 'id'), shuning
            // uchun exists:users emas, oddiy string sifatida tekshiriladi.
            // Har ikki tur (asosiy/tmz) uchun ham majburiy.
            'items.*.responsible_person_id'   => ['required', 'string', 'max:255'],
            'items.*.responsible_person_name' => ['nullable', 'string', 'max:255'],
        ];

        // type_id / category_id / model_id / tmz_id — asset_type'ga qarab
        // BIR-BIRINI ISTISNO QILADIGAN ikkita to'plam:
        //   asosiy → type_id (majburiy), category_id, model_id kerak, tmz_id TAQIQLANGAN
        //   tmz    → tmz_id (majburiy) kerak, type_id/category_id/model_id TAQIQLANGAN
        // Bu shart har bir itemning O'ZIGA bog'liq bo'lgani uchun (wildcard
        // ichida Rule::requiredIf() closure'i item indeksini bilmaydi), har bir
        // item uchun qoidani alohida, aniq indeks bilan qo'shamiz.
        foreach ($this->input('items', []) as $index => $item) {
            $isAsosiy = ($item['asset_type'] ?? null) === 'asosiy';
            $isTmz    = ($item['asset_type'] ?? null) === 'tmz';

            $rules["items.{$index}.type_id"] = [
                Rule::requiredIf($isAsosiy),
                Rule::prohibitedIf($isTmz),
                'integer', Rule::exists('types', 'id'),
            ];
            $rules["items.{$index}.category_id"] = [
                Rule::prohibitedIf($isTmz),
                'nullable', 'integer', Rule::exists('categories', 'id'),
            ];
            $rules["items.{$index}.model_id"] = [
                Rule::prohibitedIf($isTmz),
                'nullable', 'integer', Rule::exists('models', 'id'),
            ];
            $rules["items.{$index}.tmz_id"] = [
                Rule::requiredIf($isTmz),
                Rule::prohibitedIf($isAsosiy),
                'integer', Rule::exists('tmz', 'id'),
            ];
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'akt_number'                        => 'akt raqami',
            'akt_date'                          => 'akt sanasi',
            'location_id'                       => 'ombor manzili',
            'description'                       => 'izoh',
            'items'                              => 'mahsulotlar',
            'items.*.information_item_id'       => ':position-mahsulot',
            'items.*.type_id'                   => ':position-mahsulot turi',
            'items.*.category_id'               => ':position-mahsulot kategoriyasi',
            'items.*.model_id'                  => ':position-mahsulot modeli',
            'items.*.asset_type'                => ':position-mahsulot aktiv turi',
            'items.*.responsible_person_id'     => ':position-mahsulot uchun javobgar shaxs',
            'items.*.tmz_id'                    => ':position-mahsulot uchun TMZ',
        ];
    }

    public function messages(): array
    {
        return [
            'required'   => ':attribute majburiy.',
            'items.size' => "Barcha mahsulotlar (jami :size ta) uchun klassifikatsiya kiritilishi shart.",
            'distinct'   => 'Bitta mahsulot faqat bir marta ko\'rsatilishi mumkin.',
            'exists'     => 'Tanlangan :attribute mavjud emas yoki shu ma\'lumotga tegishli emas.',

            '*.type_id.required'     => 'Asosiy turi uchun turkum (type) tanlanishi shart.',
            '*.type_id.prohibited'   => "TMZ turi tanlanganda turkum (type) kiritilmasligi kerak.",
            '*.category_id.prohibited' => "TMZ turi tanlanganda kategoriya kiritilmasligi kerak.",
            '*.model_id.prohibited'    => "TMZ turi tanlanganda model kiritilmasligi kerak.",
            '*.tmz_id.required'   => 'TMZ turi tanlangan mahsulot uchun TMZ tanlanishi shart.',
            '*.tmz_id.prohibited' => "Asosiy turi tanlangan mahsulotda TMZ ko'rsatilmasligi kerak.",
        ];
    }
}
