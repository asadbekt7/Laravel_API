<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitModel extends Model
{
    protected $table = 'units';
    protected $fillable = [
        'name',
    ];
}
