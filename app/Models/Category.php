<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'points',
    ];

    protected $casts = [
        'points' => 'integer',
    ];
}
