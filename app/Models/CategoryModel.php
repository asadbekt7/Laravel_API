<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryModel extends Model
{
    protected $table = 'categories';
    protected $fillable = [
        'name',
        'type_id',
    ];

    public function type()
    {
        return $this->belongsTo(TypeModel::class, 'type_id');
    }
}
