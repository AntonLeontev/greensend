<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone',
        'channel_id',
        'name',
        'active_distribution_id',
        'last_action_id',
        'is_pending_response',
    ];

    protected $casts = [
        'is_pending_response' => 'boolean',
    ];

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
}
