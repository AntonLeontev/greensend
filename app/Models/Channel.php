<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function distributions(): HasMany
    {
        return $this->hasMany(Distribution::class);
    }

    public function chats(): HasMany
    {
        return $this->hasMany(Chat::class);
    }
}
