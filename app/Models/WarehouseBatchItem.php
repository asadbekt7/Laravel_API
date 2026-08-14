<?php
// app/Models/WarehouseBatchItem.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WarehouseBatchItem extends Model
{
    protected $fillable = ['batch_id', 'warehouse_id', 'quantity', 'talab_qilingan', 'debit', 'kredit'];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(WarehouseBatch::class, 'batch_id');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(WarehouseModel::class, 'warehouse_id');
    }
}
