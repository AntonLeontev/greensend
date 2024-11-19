<?php

namespace App\Services\OpenAI;

use App\Services\OpenAI\Enums\Model;
use App\Services\OpenAI\Schemas\ResponseFormat;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class OpenAIApi
{
    public static function models(): Response
    {
        return Http::openai()->get('/models');
    }

    public static function completion(
        string $userMessage,
        Model $model,
        string $systemMessage = '',
        int|float $temperature = 1.3,
        int $n = 1,
        int|float $maxTokens = 1000,
        int|float $presencePenalty = 2,
        int|float $frequencyPenalty = 2,
        ?ResponseFormat $responseFormat = null,
    ): Response {
        $messages = [
            [
                'role' => 'system',
                'content' => $systemMessage,
            ],
            [
                'role' => 'user',
                'content' => $userMessage,
            ],
        ];

        return self::chat($messages, $model, $temperature, $n, $maxTokens, $presencePenalty, $frequencyPenalty, $responseFormat);
    }

    public static function chat(
        Collection|array $messages,
        Model $model,
        int|float $temperature = 1.3,
        int $n = 1,
        int|float $maxTokens = 1000,
        int|float $presencePenalty = 2,
        int|float $frequencyPenalty = 2,
        ?ResponseFormat $responseFormat = null,
    ): Response {
        $data = [
            'model' => $model->value,
            'messages' => $messages,
            'temperature' => $temperature,
            'n' => $n,
            'max_tokens' => $maxTokens,
            'presence_penalty' => $presencePenalty,
            'frequency_penalty' => $frequencyPenalty,
        ];

        if ($responseFormat) {
            $data['response_format'] = $responseFormat;
        }

        return Http::openai()
            ->post('/chat/completions', $data);
    }
}
