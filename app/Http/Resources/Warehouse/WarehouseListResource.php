<?php

declare(strict_types=1);

namespace App\Http\Resources\Warehouse;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Route;

/**
 * Ro'yxat (index) uchun MAXSUS, yengil resource - "Приёмный акт" jadvalidagi
 * ustunlarga aynan mos: АКТ №, ДАТА, ДОГОВОР №, ПОСТАВЩИК, СТРОК, СУММА, ДОКУМЕНТ.
 * To'liq ma'lumot kerak bo'lsa - alohida GET /api/warehouse/{id} chaqiriladi
 * (WarehouseResource).
 */
class WarehouseListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'akt_number'      => $this->akt_number,
            'akt_date'        => $this->akt_date?->format('d.m.Y'),
            'contract_number' => $this->information?->contract_number,
            'supplier'        => $this->information?->supplier?->name,
            'items_count'     => (int) $this->items_count,
            'items_total'     => (float) $this->items_total,
            'pdf_url'         => Route::has('warehouse.receiving.pdf')
                ? route('warehouse.receiving.pdf', ['warehouse' => $this->id], absolute: false)
                : null,
        ];
    }
}
