<?php

namespace Src\ScriptNodes;

use JsonSerializable;

final class SendWhatsAppTextMessage implements JsonSerializable
{
    public static function title(): string
    {
        return 'Отправить текстовое сообщение WhatsApp';
    }

    public static function type(): string
    {
        return 'SendWhatsAppTextMessage';
    }

    public function handle(): void {}

    public function jsonSerialize(): array
    {
        return [
            'type' => self::type(),
            'class' => self::class,
            'title' => self::title(),
        ];
    }
}
