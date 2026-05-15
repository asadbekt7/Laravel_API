<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
class ReceivingModel extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'receivings';
    protected $fillable = [
        'document_type_id',
        'document_number',
        'document_date',
        'supplier_name',
        'delivery_date',
        'batch_number',
        'batch_cost',
        'notes',
        'file_path',
    ];
    protected $casts = [
        'document_type_id' => 'integer',
        'document_date'    => 'date:Y-m-d',
        'delivery_date'    => 'date:Y-m-d',
        'batch_cost'       => 'decimal:2',
    ];
    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentTypeModel::class);
    }
    public function warehouse(): HasMany
    {
        return $this->hasMany(WarehouseModel::class, 'receiving_id');
    }
}
