<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InformationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'information_id',
        'product_name',
        'unit_id',
        'quantity',
        'item_price',
        'total_price',
    ];

    protected function casts(): array
    {
        return [
            'quantity'    => 'decimal:3',
            'item_price'  => 'decimal:2',
            'total_price' => 'decimal:2',
        ];
    }

    /**
     * total_price har doim quantity * item_price ga teng bo'lishi kerak.
     * Eloquent orqali (create/update/save) yozilganda avtomatik qayta hisoblanadi.
     *
     * DIQQAT: bulk insert() bu event'ni chaqirmaydi - shuning uchun
     * Service qatlamida ham total_price aniq hisoblanadi (pastga qarang).
     */
    protected static function booted(): void
    {
        static::saving(function (self $item) {
            $item->total_price = round((float) $item->quantity * (float) $item->item_price, 2);
        });
    }

    public function information(): BelongsTo
    {
        return $this->belongsTo(InformationModel::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(UnitModel::class);
    }
}
