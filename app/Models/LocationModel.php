<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LocationModel extends Model
{
    protected $table = 'locations';
    protected $fillable = [
        'name',
    ];
}
