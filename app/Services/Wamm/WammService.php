<?php

namespace App\Services\Wamm;

use App\Models\Channel;
use Illuminate\Support\Collection;

class WammService
{
    public function __construct(public WammApi $api) {}

    public function channelList(?string $token = null): Collection
    {
        return $this->api->channelList($token)->collect('data');
    }

    public function checkPhone(string $phone, ?string $token = null): bool
    {
        if (! $token) {
            $channel = Channel::first();

            $token = $channel?->token;
        }

        return $this->api->checkPhone($phone, $token)->json('result') === 'exists';
    }

    /**
     * @return int message id
     */
    public function sendMessage(string $phone, string $text, ?int $delay = null, ?int $quoteMessageId = null, ?string $token = null): int
    {
        return $this->api->sendMessage($phone, $text, $delay, $quoteMessageId, $token)->json('msg_id');
    }
}
