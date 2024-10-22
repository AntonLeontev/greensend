<?php

namespace App\Models;

use App\Enums\UploadedFileStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UploadedFile extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'path',
        'label',
        'result_path',
        'initial_phones_number',
        'clean_phones_number',
        'whatsapp_phones_number',
        'status',
        'whatsapp_check_percent',
    ];

    protected $casts = [
        'status' => UploadedFileStatus::class,
    ];
}
