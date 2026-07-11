<?php

namespace App\Models;

use App\Http\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WarehouseModel extends Model
{
    protected $table = 'warehouse';

    protected $fillable = [
        'information_id',
        'assignee_id',
        'name',
        'type_id',
        'category_id',
        'model_id',
        'quantity',
        'unit_id',
        'location_id',
        'product_price',
        'description',
    ];

    protected $casts = [
        'quantity'      => 'integer',
        'staff_id'      => 'integer',
        'product_price' => 'decimal:2',
    ];

    public function scopeFilter(Builder $query, QueryFilter $filter): Builder
    {
        return $filter->apply($query);
    }

    public function information(): BelongsTo
    {
        return $this->belongsTo(InformationModel::class, 'information_id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(TypeModel::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CategoryModel::class);
    }

    public function model(): BelongsTo
    {
        return $this->belongsTo(GoodModel::class, 'model_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(UnitModel::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(LocationModel::class);
    }
}
