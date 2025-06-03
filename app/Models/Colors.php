<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Colors extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'color_ID';

    protected $fillable = ['color_ID','color_name'];
}
