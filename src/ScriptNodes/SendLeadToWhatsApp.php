<?php

namespace Src\ScriptNodes;

class SendLeadToWhatsApp extends ScriptNode
{
    public function __construct(private string $phone, private int $distributionId, private int $nodeId) {}

    public static function title(): string
    {
        return 'Отправить контакт в WhatsApp';
    }

    public static function type(): string
    {
        return 'SendLeadToWhatsApp';
    }
}
