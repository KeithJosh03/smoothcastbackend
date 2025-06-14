<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'brand_ID';
    protected $fillable = ['brand_name'];
}
