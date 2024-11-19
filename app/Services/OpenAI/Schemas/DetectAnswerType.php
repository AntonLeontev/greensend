<?php

namespace App\Services\OpenAI\Schemas;

class DetectAnswerType implements ResponseFormat
{
    public function jsonSerialize(): array
    {
        return [
            'type' => 'json_schema',
            'json_schema' => [
                'name' => 'response_type_determining',
                'description' => 'Determining the type of user response',
                'schema' => [
                    'type' => 'object',
                    'properties' => [
                        'type' => [
                            'type' => ['string', 'null'],
                            'description' => "Type of the client's response. yes - if the user agrees, no - if the user disagrees. null if unable to determine",
                            'enum' => ['yes', 'no', 'default'],
                        ],
                    ],
                    'required' => ['type'],
                    'additionalProperties' => false,
                ],
                'strict' => true,
            ],
        ];
    }
}
