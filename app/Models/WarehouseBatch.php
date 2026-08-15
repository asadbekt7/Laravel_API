<?php
// app/Models/WarehouseBatch.php

namespace App\Models;

use App\Enums\BatchStatus;
use App\Enums\SignerLevelStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

class WarehouseBatch extends Model
{
    protected $fillable = ['batch_number', 'created_by', 'status', 'file_path', 'completed_at'];

    protected $casts = [
        'status' => BatchStatus::class,
        'completed_at' => 'datetime',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function entries(): HasMany
    {
        return $this->hasMany(BugalteriyaModel::class, 'batch_id');
    }

    public function signers(): HasMany
    {
        return $this->hasMany(WarehouseBatchSigner::class, 'batch_id')->orderBy('level');
    }

    public function activeSigner(): HasOne
    {
        return $this->hasOne(WarehouseBatchSigner::class, 'batch_id')
            ->where('status', SignerLevelStatus::Active);
    }

    /** batch_id bo'yicha qidirilganda — tasniflangan TOVARLAR (items) */
    public function items(): Collection
    {
        $itemIds = $this->entries()->whereNotNull('items_id')->pluck('items_id');

        return ItemsModel::whereIn('id', $itemIds)->get();
    }
}
