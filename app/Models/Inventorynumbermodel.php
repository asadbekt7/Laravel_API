<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventorynumbermodel extends Model
{
    protected $table = 'inventorynumbers';
    protected $fillable = ['inventory_number'];
}
