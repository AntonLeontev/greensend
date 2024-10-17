<?php

namespace App\Services\Wamm;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class WammApi
{
    public function channelList(): Response
    {
        return Http::wammGet('channel_list');
    }

    public function checkPhone(string $phone): Response
    {
        return Http::wammGet('check_phone', [
            'phone' => $phone,
        ]);
    }

    public function sendMessage(string $phone, string $text): Response
    {
        return Http::wammGet('msg_to', [
            'phone' => $phone,
            'text' => $text,
        ]);
    }
}
