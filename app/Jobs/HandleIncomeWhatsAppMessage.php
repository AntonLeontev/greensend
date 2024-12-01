<?php

namespace App\Jobs;

use App\DTO\WammWhatsAppMessageDTO;
use App\Enums\DistributionType;
use App\Enums\MessageStatus;
use App\Models\Channel;
use App\Models\Chat;
use App\Models\Distribution;
use App\Models\Message;
use App\Services\ConversationScriptService;
use App\Services\OpenAI\OpenAIService;
use App\Services\Wamm\WammService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class HandleIncomeWhatsAppMessage implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public WammWhatsAppMessageDTO $message) {}

    /**
     * Execute the job.
     */
    public function handle(
        ConversationScriptService $conversationScriptService,
        OpenAIService $openai,
        WammService $wamm,
    ): void {
        if ($this->message->fromApi && $this->message->fromMe) {
            return;
        }

        $channel = Channel::where('number', $this->message->phoneAcc)
            ->first();

        if ($channel === null) {
            return;
        }

        $chat = Chat::firstOrCreate(
            ['phone' => $this->message->phone, 'channel_id' => $channel->id],
            [
                'active_distribution_id' => null,
                'last_action_id' => null,
                'name' => $this->message->phone,
            ]
        );

        Message::create([
            'chat_id' => $chat->id,
            'text' => $this->message->text,
            'is_incoming' => ! $this->message->fromMe,
            'wamm_message_id' => $this->message->id,
            'status' => MessageStatus::from($this->message->state),
            'distribution_id' => $chat->active_distribution_id,
        ]);

        if ($chat->is_pending_response) {
            return;
        }

        if ($this->message->fromMe) {
            return;
        }

        if ($chat->active_distribution_id === null) {
            return;
        }

        $distribution = Distribution::find($chat->active_distribution_id);

        if ($distribution->type === DistributionType::SCRIPT) {
            $conversation = $distribution->data->conversation;

            $lastAction = $conversationScriptService->findAction($chat->last_action_id, $conversation);
            $nextActions = collect($lastAction->children);

            if ($nextActions->contains(fn ($el) => $el->condition === 'yes' || $el->condition === 'no')) {
                $condition = $openai->determineAnswerType($lastAction->action->data->text, $this->message->text);

                $nextAction = $nextActions->first(fn ($el) => $el->condition === $condition->value);
            } else {
                $nextAction = $nextActions->first(fn ($el) => $el->condition === 'default');
            }

            if ($nextAction === null) {
                $chat->active_distribution_id = null;
                $chat->save();

                return;
            }

            $chat->update(['is_pending_response' => true]);

            $class = $nextAction->action->class;
            dispatch(new $class($chat->phone, $distribution->id, $nextAction->id));

            $chat->last_action_id = $nextAction->id;
            $chat->save();
        }

        if ($distribution->type === DistributionType::AI) {
            $messagesCount = Message::where('distribution_id', $distribution->id)
                ->where('chat_id', $chat->id)
                ->count();

            if ($messagesCount >= 30) {
                return;
            }

            $chat->update(['is_pending_response' => true]);

            $messagesForSend = collect();

            $messagesForSend->push([
                'role' => 'system',
                'content' => $distribution->data->system_message,
            ]);

            $messages = Message::where('distribution_id', $distribution->id)
                ->get();

            foreach ($messages as $message) {
                $messagesForSend->push([
                    'role' => $message->is_incoming ? 'user' : 'assistant',
                    'content' => $message->text,
                ]);
            }

            $answer = $openai->chatComplete($messagesForSend);

            $message = Message::create([
                'chat_id' => $chat->id,
                'text' => $answer,
                'is_incoming' => false,
                'distribution_id' => $distribution->id,
                'status' => MessageStatus::INIT,
            ]);

            try {
                $wammMessageId = $wamm->sendMessage(
                    phone: $chat->phone,
                    text: $answer,
                    delay: 20,
                    token: $channel->token,
                );
            } catch (\Throwable $th) {
                $message->update(['status' => MessageStatus::ERROR]);
                $chat->update(['is_pending_response' => false]);

                $this->fail("Не удалось отправить сообщение в Wamm. Message id: {$message->id}. Причина: {$th->getMessage()}");
            }

            $message->update([
                'wamm_message_id' => $wammMessageId,
                'status' => MessageStatus::SENT,
            ]);
        }
    }
}
