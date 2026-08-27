<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\WarehouseAktStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WarehouseModel extends Model
{
    protected $table = 'warehouse';

    protected $fillable = [
        'information_id',
        'location_id',
        'description',
        'assignee_id',
        'akt_number',
        'akt_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'akt_date' => 'date',
            'status'   => WarehouseAktStatus::class,
        ];
    }

    public function information(): BelongsTo
    {
        return $this->belongsTo(InformationModel::class, 'information_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(LocationModel::class, 'location_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'assignee_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(WarehouseItem::class, 'warehouse_id');
    }
}
