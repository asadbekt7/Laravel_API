<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierModel extends Model
{
    protected $table = 'suppliers';
    protected $fillable = ['name'];
}
