<?php
// app/Models/WarehouseBatchSigner.php
namespace App\Models;

use App\Enums\SignerLevelStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WarehouseBatchSigner extends Model
{
    protected $fillable = ['batch_id', 'user_id', 'level', 'role_label', 'status', 'comment', 'responded_at'];

    protected $casts = [
        'status' => SignerLevelStatus::class,
        'responded_at' => 'datetime',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(WarehouseBatch::class, 'batch_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
