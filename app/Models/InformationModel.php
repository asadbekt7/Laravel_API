<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\InformationStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
        'creator_id', 'status', 'reject_reason',
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
        return $this->belongsTo(SupplierModel::class, 'supplier_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'creator_id');
    }

    public function warehouse(): HasOne
    {
        return $this->hasOne(WarehouseModel::class, 'information_id');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', InformationStatus::Pending);
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', InformationStatus::Rejected);
    }

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

    /**
     * Ombor tomonidan rad etish — warehouse jadvaliga umuman tegmaydi,
     * faqat status + sababni yozadi.
     */
    public function reject(string $reason): bool
    {
        if (! $this->status->canTransitionTo(InformationStatus::Rejected)) {
            return false;
        }

        return $this->update([
            'status'        => InformationStatus::Rejected,
            'reject_reason' => $reason,
        ]);
    }

    /**
     * Rad etilgan ma'lumotni foydalanuvchi to'g'irlab, qayta "pending"
     * navbatiga qaytarishi (resubmit). reject_reason tozalanadi.
     */
    public function resubmit(): bool
    {
        if (! $this->status->canTransitionTo(InformationStatus::Pending)) {
            return false;
        }

        return $this->update([
            'status'        => InformationStatus::Pending,
            'reject_reason' => null,
        ]);
    }
}
