<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InformationModel extends Model
{
    use SoftDeletes;

    protected $table = 'information';

    protected $fillable = [
        'name',
        'contract_number',
        'contract_date',
        'contract_file_path',
        'contract_file_name',
        'supplier_id',
        'bildirishnoma_number',
        'bildirishnoma_date',
        'bildirishnoma_file_path',
        'bildirishnoma_file_name',
        'product_name',
        'unit_id',
        'quantity',
        'price',
        'ishonchnoma_number',
        'ishonchnoma_date',
        'ishonchnoma_file_path',
        'ishonchnoma_file_name',
        'hisob_faktura',
        'hisob_faktura_date',
        'hisob_faktura_file_path',
        'hisob_faktura_file_name',
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
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(SupplierModel::class);
    }
    public function unit(): BelongsTo
    {
        return $this->belongsTo(UnitModel::class);
    }
}
