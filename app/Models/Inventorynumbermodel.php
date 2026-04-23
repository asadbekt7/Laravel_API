<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inventorynumbermodel extends Model
{
    protected $table = 'inventorynumbers';
    protected $fillable = ['inventory_number'];
    public function computer(): HasOne{
        return $this->hasOne(Computermodel::class, 'inventory_number_id');
    }
}
