<?php

namespace App\Jobs;

use App\DTO\WammWhatsAppMessageDTO;
use App\Enums\MessageStatus;
use App\Models\Channel;
use App\Models\Chat;
use App\Models\Distribution;
use App\Models\Message;
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
    public function handle(): void
    {
        if ($this->message->fromApi && $this->message->fromMe) {
            return;
        }

        $channel = Channel::where('number', $this->message->phoneAcc)
            ->first();

        if ($channel === null) {
            return;
        }

        $chat = Chat::where('phone', $this->message->phone)
            ->where('channel_id', $channel->id)
            ->first();

        if ($chat === null) {
            $chat = Chat::create([
                'channel_id' => $channel->id,
                'phone' => $this->message->phone,
                'active_distribution_id' => null,
                'last_action_id' => null,
            ]);
        }

        $message = Message::create([
            'chat_id' => $chat->id,
            'text' => $this->message->text,
            'is_incoming' => ! $this->message->fromMe,
            'wamm_message_id' => $this->message->id,
            'status' => MessageStatus::from($this->message->state),
        ]);

        if ($this->message->fromMe) {
            return;
        }

        if ($chat->active_distribution_id === null) {
            return;
        }

        $distribution = Distribution::find($chat->active_distribution_id);

        // Запустить обработку ответа, если это ответ
    }
}
