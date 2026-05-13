<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WarehouseModel extends Model
{
    protected $table = 'warehouse';
    protected $fillable = [
        'name',
        'category_id',
        'model_id',
        'unit_id',
        'quantity',
        'staff_id',
        'product_price',
        'description'
    ];
    public function category(){
        return $this->belongsTo(Categorymodel::class,'category_id','id');
    }
    public function model()
    {
        return $this->belongsTo(Itemmodel::class, 'model_id', 'id');
    }

    public function unit()
    {
        return $this->belongsTo(Unitmodel::class, 'unit_id', 'id');
    }

}
