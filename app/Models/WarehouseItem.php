<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WarehouseItem extends Model
{
    protected $table = 'warehouse_items';

    protected $fillable = [
        'warehouse_id',
        'information_item_id',
        'type_id',
        'category_id',
        'model_id',
    ];

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(WarehouseModel::class, 'warehouse_id');
    }

    public function informationItem(): BelongsTo
    {
        return $this->belongsTo(InformationItem::class, 'information_item_id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(TypeModel::class, 'type_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CategoryModel::class, 'category_id');
    }

    public function model(): BelongsTo
    {
        return $this->belongsTo(GoodModel::class, 'model_id');
    }
}
