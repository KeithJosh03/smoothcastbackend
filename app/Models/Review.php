<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    public $timestamps = true;
    protected $primaryKey = 'review_id';

    protected $fillable = [
        'reviewer_name',
        'rating',
        'review_date',
        'comment',
    ];

    protected $casts = [
        'review_date' => 'date',
        'rating' => 'integer',
    ];
}
