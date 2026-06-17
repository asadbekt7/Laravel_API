<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MalumotModel extends Model
{
    use HasFactory;

    protected $table = 'malumots';

    protected $fillable = [
        'contract_number',
        'contract_date',
        'contract_file_path',
        'yetkazuvchi_id',
        'bildirishnoma_number',
        'bildirishnoma_date',
        'bildirishnoma_file_path',
        'name',
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
        'contract_date'       => 'date',
        'bildirishnoma_date'  => 'date',
        'ishonchnoma_date'    => 'date',
        'hisob_faktura_date'  => 'date',
        'akt_date'            => 'date',
        'quantity'            => 'integer',
        'price'               => 'decimal:2',
        'created_at'          => 'datetime',
        'updated_at'          => 'datetime',
    ];
    public function yetkazuvchi(): BelongsTo
    {
        return $this->belongsTo(YetkazuvchiModel::class, 'yetkazuvchi_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(UnitModel::class, 'unit_id');
    }
}
