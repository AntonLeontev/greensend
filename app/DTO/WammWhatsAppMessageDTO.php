<?php

namespace App\DTO;

readonly class WammWhatsAppMessageDTO
{
    public function __construct(
        public int $id,
        public bool $fromApi,
        public bool $fromMe,
        public string $phoneAcc,
        public string $phone,
        public ?string $chatName,
        public ?string $tipMsg,
        public ?string $text,
        public ?string $link,
        public ?string $date,
        public ?string $state,
    ) {}

    public static function fromWebhookRequest(array $requestData): self
    {
        return new static(
            $requestData['id'],
            (bool) $requestData['from_api'],
            (bool) $requestData['from_me'],
            $requestData['phone_acc'],
            $requestData['phone'],
            $requestData['chat_name'],
            $requestData['tip_msg'],
            $requestData['msg_text'],
            $requestData['msg_link'],
            $requestData['date_ins'],
            $requestData['state'],
        );
    }
}
