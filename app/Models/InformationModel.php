<?php

namespace App\Models;

use App\Http\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class InformationModel extends Model
{
    use SoftDeletes;
    protected $table = 'informations';
    protected $fillable = [
        'akt_raqam',
        'akt_sana',
        'schet_faktura_raqam',
        'schet_faktura_sana',
        'supplier_id',
        'shartnoma_raqam',
        'shartnoma_sana',
        'shartnoma_fayl',
        'xaridor_full_name',
        'ishonchnoma_raqam',
        'ishonchnoma_sana',
        'ishonchnoma_fayl',
        'location_id',
        'ombor_qabul_sana',
        'ombor_qabul_xodim_full_name',
        'jami_summa',
    ];
    protected $casts = [
        'akt_sana'               => 'date',
        'schet_faktura_sana'   => 'date',
        'shartnoma_sana'         => 'date',
        'ishonchnoma_sana'       => 'date',
        'ombor_qabul_sana'     => 'date',
    ];
    public function scopeFilter(Builder $query, QueryFilter $filter): Builder
    {
        return $filter->apply($query);
    }
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(SupplierModel::class);
    }
    public function location(): BelongsTo
    {
        return $this->belongsTo(LocationModel::class);
    }
}
