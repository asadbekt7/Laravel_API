<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\InformationStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class InformationModel extends Model
{
    use SoftDeletes;

    protected $table = 'information';

    protected $fillable = [
        'name', 'description',
        'contract_number', 'contract_date', 'contract_file_path', 'contract_file_name',
        'supplier_id',
        'bildirishnoma_number', 'bildirishnoma_date', 'bildirishnoma_file_path', 'bildirishnoma_file_name',
        'ishonchnoma_number', 'ishonchnoma_date', 'ishonchnoma_file_path', 'ishonchnoma_file_name',
        'hisob_faktura', 'hisob_faktura_date', 'hisob_faktura_file_path', 'hisob_faktura_file_name',
        'creator_id', 'status',
    ];

    protected function casts(): array
    {
        return [
            'contract_date'      => 'date',
            'bildirishnoma_date' => 'date',
            'ishonchnoma_date'   => 'date',
            'hisob_faktura_date' => 'date',
            'accepted_at'        => 'datetime',
            'completed_at'       => 'datetime',
            'status'             => InformationStatus::class,
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $information) {
            $information->creator_id ??= auth()->id();
            $information->status ??= InformationStatus::Pending;
        });
    }

    public function items(): HasMany
    {
        return $this->hasMany(InformationItem::class, 'information_id');
    }

    public function supplier(): BelongsTo
    {
        // Loyihangizdagi haqiqiy Supplier model nomiga moslang.
        return $this->belongsTo(SupplierModel::class, 'supplier_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'creator_id');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', InformationStatus::Pending);
    }

    /**
     * Statuslar orasidagi o'tish — enum ichidagi TRANSITIONS jadvaliga tayanadi.
     * Ruxsat etilmagan o'tishda false qaytaradi (Controller shu yerda 422 beradi).
     */
    public function transitionTo(InformationStatus $target): bool
    {
        if (! $this->status->canTransitionTo($target)) {
            return false;
        }

        $attributes = ['status' => $target];

        if ($target === InformationStatus::Accepted) {
            $attributes['accepted_at'] = now();
        }

        if ($target === InformationStatus::Completed) {
            $attributes['completed_at'] = now();
        }

        return $this->update($attributes);
    }
}
