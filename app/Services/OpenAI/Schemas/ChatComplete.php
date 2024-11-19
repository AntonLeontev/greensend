<?php

namespace App\Services\OpenAI\Schemas;

class ChatComplete implements ResponseFormat
{
    public function jsonSerialize(): array
    {
        return [
            'type' => 'json_schema',
            'json_schema' => [
                'name' => 'chat_complete',
                'description' => 'completing chat with user',
                'schema' => [
                    'type' => 'object',
                    'properties' => [
                        'answer' => [
                            'type' => ['string'],
                            'description' => "The answer to the user's message",
                        ],
                    ],
                    'required' => ['answer'],
                    'additionalProperties' => false,
                ],
                'strict' => true,
            ],
        ];
    }
}
