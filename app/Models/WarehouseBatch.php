<?php
// app/Models/WarehouseBatch.php
namespace App\Models;

use App\Enums\BatchStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class WarehouseBatch extends Model
{
    protected $fillable = ['batch_number', 'staff_id', 'created_by', 'status', 'pdf_path', 'completed_at'];

    protected $casts = [
        'status' => BatchStatus::class,
        'completed_at' => 'datetime',
    ];

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(WarehouseBatchItem::class, 'batch_id');
    }

    public function signers(): HasMany
    {
        return $this->hasMany(WarehouseBatchSigner::class, 'batch_id')->orderBy('level');
    }

    public function activeSigner(): HasOne
    {
        return $this->hasOne(WarehouseBatchSigner::class, 'batch_id')
            ->where('status', \App\Enums\SignerLevelStatus::Active);
    }
}
