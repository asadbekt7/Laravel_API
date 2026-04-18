<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Itemmodel extends Model
{
    protected $table = 'models';
    protected $fillable =
        [
            'name',
            'category_id'
        ];
    public function category(){
        return $this->belongsTo(Categorymodel::class, 'category_id');
    }
}
