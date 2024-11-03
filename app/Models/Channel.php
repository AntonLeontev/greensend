<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'label',
        'token',
        'is_main',
    ];

    protected $casts = [
        'is_main' => 'boolean',
    ];
}
