<?php

namespace App\Models;

use App\Enums\ItemType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BugalteriyaModel extends Model
{
    protected $table = 'bugalteriya';

    protected $guarded = ['id'];

    protected $casts = [
        'item_type'    => ItemType::class,
        'expiry_date'  => 'date',
        'completed_at' => 'datetime',
    ];

    public const STATUS_PENDING   = 'pending';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(WarehouseModel::class, 'warehouse_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(ItemsModel::class, 'items_id');
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

    public function unit(): BelongsTo
    {
        return $this->belongsTo(UnitModel::class, 'unit_id');
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }
}
