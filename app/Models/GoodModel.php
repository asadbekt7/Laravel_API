<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodModel extends Model
{
    protected $table = 'models';
    protected $fillable =
        [
            'name',
            'category_id'
        ];
    public function category(){
        return $this->belongsTo(CategoryModel::class, 'category_id');
    }
}
// GoodModel = Model ga
