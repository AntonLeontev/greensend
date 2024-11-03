<?php

namespace App\Services\Wamm;

use App\Services\Wamm\Enums\Delay;
use Illuminate\Support\Collection;

class WammService
{
    public function __construct(public WammApi $api) {}

    public function channelList(): Collection
    {
        return $this->api->channelList()->collect('data');
    }

    public function checkPhone(string $phone): bool
    {
        return $this->api->checkPhone($phone)->json('result') === 'exists';
    }

    /**
     * @return int message id
     */
    public function sendMessage(string $phone, string $text, ?Delay $delay = null, ?int $quoteMessageId = null): int
    {
        return $this->api->sendMessage($phone, $text, $delay, $quoteMessageId)->json('msg_id');
    }
}
