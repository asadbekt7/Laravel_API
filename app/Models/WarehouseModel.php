<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WarehouseModel extends Model
{
    protected $table = 'warehouse';

    protected $fillable = [
        'receiving_id',
        'name',
        'type_id',
        'category_id',
        'model_id',
        'receiving_supplier_name',
        'quantity',
        'unit_id',
        'condition',
        'location_id',
        'staff_id',
        'price_per_unit',
        'product_price',
        'description',
    ];

    protected $casts = [
        'quantity'       => 'integer',
        'staff_id'       => 'integer',
        'price_per_unit' => 'decimal:2',
        'product_price'  => 'decimal:2',
    ];
    public function receiving(): BelongsTo
    {
        return $this->belongsTo(ReceivingModel::class, 'receiving_id');
    }
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(ReceivingModel::class, 'receiving_supplier_name');
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
    public function location(): BelongsTo
    {
        return $this->belongsTo(LocationModel::class, 'location_id');
    }
}
