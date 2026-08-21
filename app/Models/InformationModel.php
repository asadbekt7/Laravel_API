<?php

namespace App\Models;

use App\Enums\InformationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class InformationModel extends Model
{
    use HasFactory, SoftDeletes;

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
        'price'              => 'decimal:2',
        'quantity'           => 'integer',
        'status'             => InformationStatus::class,
        'accepted_at'        => 'datetime',
        'completed_at'       => 'datetime',
    ];

    /**
     * Model hodisalari — avtomatik to'ldirish shu yerda.
     */
    protected static function booted(): void
    {
        static::creating(function (InformationModel $information) {
            if (auth()->check() && empty($information->creator_id)) {
                $information->creator_id = auth()->id();
            }

            // Status avtomatik "pending" bo'ladi
            if (empty($information->status)) {
                $information->status = InformationStatus::Pending;
            }
        });
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(SupplierModel::class, 'supplier_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(UnitModel::class, 'unit_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    /* ---------------- Status boshqaruvi ---------------- */

    public function accept(): bool
    {
        if ($this->status !== InformationStatus::Pending) {
            return false;
        }

        return $this->forceFill([
            'assignee_id' => auth()->id(),
            'status'      => InformationStatus::Accepted,
            'accepted_at' => now(),
        ])->save();
    }

    public function startProgress(): bool
    {
        if ($this->status !== InformationStatus::Accepted) {
            return false;
        }

        if (auth()->id() !== $this->assignee_id) {
            return false;
        }

        return $this->forceFill([
            'status' => InformationStatus::InProgress,
        ])->save();
    }

    public function complete(): bool
    {
        if (! in_array($this->status, [InformationStatus::Accepted, InformationStatus::InProgress])) {
            return false;
        }

        if (auth()->id() !== $this->assignee_id) {
            return false;
        }

        return $this->forceFill([
            'status'       => InformationStatus::Completed,
            'completed_at' => now(),
        ])->save();
    }

    /* ---------------- Scope'lar ---------------- */

    public function scopePending($query)
    {
        return $query->where('status', InformationStatus::Pending);
    }

    /**
     * Faqat butun qabul-topshirish jarayoni yakunlangan (akt tayyor) yozuvlar.
     * /receiving endpoint'i faqat shu holatdagi ma'lumotlarni ko'rsatishi kerak
     * (jarayon tugamagan akt hali "rasmiy" hisoblanmaydi).
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', InformationStatus::Completed);
    }

    public function scopeAssignedTo($query, int $userId)
    {
        return $query->where('assignee_id', $userId);
    }
}
