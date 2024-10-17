<?php

namespace App\Services\Wamm;

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
    public function sendMessage(string $phone, string $text): int
    {
        return $this->api->sendMessage($phone, $text)->json('msg_id');
    }
}
