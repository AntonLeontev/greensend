<?php

namespace App\Services\Wamm;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class WammApi
{
    public function channelList(?string $token = null): Response
    {
        return Http::wammGet('channel_list', [], $token);
    }

    public function checkPhone(string $phone, ?string $token = null): Response
    {
        return Http::wammGet('check_phone', [
            'phone' => $phone,
        ], $token);
    }

    public function sendMessage(string $phone, string $text, ?int $delay = null, ?int $quoteMessageId = null, ?string $token = null): Response
    {
        $data = [
            'phone' => $phone,
            'text' => $text,
        ];

        if ($delay) {
            $data['delay'] = $delay;
        }

        if ($quoteMessageId) {
            $data['quote_msg_id'] = $quoteMessageId;
        }

        return Http::wammGet('msg_to', $data, $token);
    }
}
