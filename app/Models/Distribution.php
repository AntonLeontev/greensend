<?php

namespace App\Models;

use App\Enums\DistributionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Distribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'uploaded_file_id',
        'is_sent',
        'type',
        'starts_at',
        'data',
    ];

    protected $casts = [
        'is_sent' => 'boolean',
        'type' => DistributionType::class,
        'starts_at' => 'datetime',
        'data' => 'object',
    ];
}
