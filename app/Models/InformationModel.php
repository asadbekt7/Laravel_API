<?php

namespace App\Models;

use App\Http\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InformationModel extends Model
{
    use SoftDeletes;

    protected $table = 'informations';

    protected $fillable = [
        'name',
        'contract_number',
        'contract_date',
        'contract_file_path',
        'supplier_id',
        'bildirishnoma_number',
        'bildirishnoma_date',
        'bildirishnoma_file_path',
        'product_name',
        'unit_id',
        'quantity',
        'price',
        'ishonchnoma_number',
        'ishonchnoma_date',
        'ishonchnoma_file_path',
        'hisob_faktura',
        'hisob_faktura_date',
        'hisob_faktura_file_path',
        'akt_number',
        'akt_date',
        'description',
    ];

    protected $casts = [
        'contract_date'      => 'date',
        'bildirishnoma_date' => 'date',
        'ishonchnoma_date'   => 'date',
        'hisob_faktura_date' => 'date',
        'akt_date'           => 'date',
        'quantity'           => 'integer',
        'price'              => 'decimal:2',
    ];
    public function scopeFilter(Builder $query, QueryFilter $filter): Builder
    {
        return $filter->apply($query);
    }
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(SupplierModel::class);
    }
    public function unit(): BelongsTo
    {
        return $this->belongsTo(UnitModel::class);
    }
}
