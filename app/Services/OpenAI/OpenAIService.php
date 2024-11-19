<?php

namespace App\Services\OpenAI;

use App\Enums\ConversationScriptCondition;
use App\Services\OpenAI\Enums\Model;
use App\Services\OpenAI\Schemas\ChatComplete;
use App\Services\OpenAI\Schemas\DetectAnswerType;
use App\Services\OpenAI\Schemas\ResponseFormat;
use Illuminate\Support\Collection;

class OpenAIService
{
    public function __construct(public OpenAIApi $api) {}

    public function complete(string $message, Model $model = Model::GPT_4O_MINI): string|array|int|float
    {
        $response = $this->api->completion(
            $message,
            temperature: 1,
            presencePenalty: 1,
            frequencyPenalty: 1,
            model: $model
        );

        return $response->json();
    }

    public function completion(string $userMessage, string $systemMessage, Model $model, int $n = 1, ?ResponseFormat $responseFormat = null)
    {
        $response = $this->api->completion($userMessage, $model, $systemMessage, n: $n, responseFormat: $responseFormat);

        return $response->object();
    }

    public function determineAnswerType(string $question, string $answer): ConversationScriptCondition
    {
        $systemMessage = "We will send the message that the user received and his answer to the message. Your task is to determine the type of the user's response. If he agrees, then return 'yes'. If he disagrees, then return 'no'. If you can't figure it out, then return 'default'";

        $userMessage = "message: '$question'; answer: '$answer'";

        $response = $this->completion(
            $userMessage,
            $systemMessage,
            Model::GPT_4O_MINI,
            responseFormat: new DetectAnswerType,
        );

        $type = json_decode($response->choices[0]->message->content)->type;
        $condition = ConversationScriptCondition::tryFrom($type);

        return $condition ?? ConversationScriptCondition::DEFAULT;
    }

    public function chatComplete(Collection|array $messages): string
    {
        $response = $this->api->chat($messages, Model::GPT_4O_MINI, responseFormat: new ChatComplete)
            ->object();

        return json_decode($response->choices[0]->message->content)->answer;
    }
}
