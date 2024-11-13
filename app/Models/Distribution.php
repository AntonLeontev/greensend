<?php

namespace App\Models;

use App\Enums\DistributionStatus;
use App\Enums\DistributionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Distribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'uploaded_file_id',
        'channel_id',
        'type',
        'starts_at',
        'data',
        'status',
    ];

    protected $casts = [
        'is_sent' => 'boolean',
        'type' => DistributionType::class,
        'starts_at' => 'datetime',
        'data' => 'object',
        'status' => DistributionStatus::class,
    ];

    public function uploadedFile(): BelongsTo
    {
        return $this->belongsTo(UploadedFile::class);
    }

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }
}
