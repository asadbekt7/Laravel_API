<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemsModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'items';

    protected $fillable = [
        'uzasbo_import_id',
        'receiving_id',
        'name',
        'type_id',
        'category_id',
        'model_id',
        'quantity',
        'unit_id',
        'inventory_number',
        'room_name',
        'building',
        'room_number',
        'last_name',
        'first_name',
        'middle_name',
        'full_name',
        'condition',
        'status',
        'notes',
    ];

    protected $casts = [
        'quantity'  => 'integer',
        'condition' => 'string',
        'status'    => 'string',
    ];

    public function uzasboImport(): BelongsTo
    {
        return $this->belongsTo(UzasboImportModel::class, 'uzasbo_import_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(UnitModel::class, 'unit_id');
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
// ItemsModel = umumiy narsalalrni modeliga
