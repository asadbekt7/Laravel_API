<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Models extends Model
{
    protected $table = 'models';
    protected $fillable =
        [
            'name',
            'categories_id'
        ];
    public function categories(){
        return $this->belongsTo(Categoriesmodel::class, 'categories_id');
    }
}
