<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    public function lastMessage(): HasOne
    {
        return $this->hasOne(Message::class)
            ->select(['text', 'chat_id', 'created_at', 'is_incoming', 'status'])
            ->orderBy('id', 'desc')
            ->limit(1);
    }
}
