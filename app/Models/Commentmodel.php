<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commentmodel extends Model
{
    protected $table = 'comment';
    protected $fillable = ['comment'];
    public $timestamps = false;
}
