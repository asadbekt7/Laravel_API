<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeModel extends Model
{
    protected $table = 'types';
    protected $fillable = [
        'name',
    ];
}
