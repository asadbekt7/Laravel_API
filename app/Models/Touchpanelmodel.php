<?php
// App/Models/Touchpanelmodel.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Touchpanelmodel extends Model
{
    use HasFactory;
    protected $table = 'touchpanels';

    protected $fillable = [
        'name', 'category_id', 'model_id', 'unit_id',
        'quantity', 'description', 'room_name', 'building',
        'room_number', 'inventory_number_id',
    ];

    protected $casts = [
        'quantity'   => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function category(): BelongsTo { return $this->belongsTo(Categorymodel::class); }
    public function model(): BelongsTo { return $this->belongsTo(Itemmodel::class, 'model_id'); }
    public function unit(): BelongsTo { return $this->belongsTo(Unitmodel::class); }
    public function inventoryNumber(): BelongsTo { return $this->belongsTo(Inventorynumbermodel::class, 'inventory_number_id'); }
}
