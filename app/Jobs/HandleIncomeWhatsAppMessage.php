<?php

namespace App\Jobs;

use App\DTO\WammWhatsAppMessageDTO;
use App\Enums\MessageStatus;
use App\Models\Channel;
use App\Models\Chat;
use App\Models\Distribution;
use App\Models\Message;
use App\Services\ConversationScriptService;
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
    public function handle(ConversationScriptService $conversationScriptService): void
    {
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
        $conversation = $distribution->data->conversation;

        $lastAction = $conversationScriptService->findAction($chat->last_action_id, $conversation);
        $nextActions = collect($lastAction->children);

        if ($nextActions->isEmpty()) {
            $chat->active_distribution_id = null;
            $chat->save();

            return;
        }

        if ($nextActions->contains(fn ($el) => $el->condition === 'yes' || $el->condition === 'no')) {
            //TODO Проверить ответ в gpt
        }

        if ($nextActions->contains(fn ($el) => $el->condition === 'default')) {
            $nextAction = $nextActions->first(fn ($el) => $el->condition === 'default');
        }

        $chat->update(['is_pending_response' => true]);

        $class = $nextAction->action->class;
        dispatch(new $class($chat->phone, $distribution->id, $nextAction->id, $distribution->channel_id));

        $chat->last_action_id = $nextAction->id;
        $chat->save();
    }
}
