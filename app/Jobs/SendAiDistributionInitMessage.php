<?php

namespace App\Jobs;

use App\Enums\MessageStatus;
use App\Models\Channel;
use App\Models\Chat;
use App\Models\Distribution;
use App\Models\Message;
use App\Services\Wamm\WammService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendAiDistributionInitMessage implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $phone, public int $distributionId) {}

    /**
     * Execute the job.
     */
    public function handle(WammService $wamm): void
    {
        $distribution = Distribution::find($this->distributionId);
        $channel = Channel::find($distribution->channel_id);

        $chat = Chat::where('channel_id', $channel->id)
            ->where('phone', $this->phone)
            ->first();

        if ($chat) {
            $chat->update([
                'active_distribution_id' => $this->distributionId,
            ]);
        } else {
            $chat = Chat::create([
                'channel_id' => $channel->id,
                'phone' => $this->phone,
                'name' => $this->phone,
                'active_distribution_id' => $this->distributionId,
            ]);
        }

        $message = Message::create([
            'chat_id' => $chat->id,
            'text' => $distribution->data->first_message,
            'is_incoming' => false,
            'status' => MessageStatus::INIT,
            'distribution_id' => $chat->active_distribution_id,
        ]);

        try {
            $wammMessageId = $wamm->sendMessage(
                phone: $this->phone,
                text: $distribution->data->first_message,
                delay: 30,
                token: $channel->token,
            );
        } catch (\Throwable $th) {
            $message->update(['status' => MessageStatus::ERROR]);

            $this->fail("Не удалось отправить сообщение в Wamm. Message id: {$message->id}. Причина: {$th->getMessage()}");
        }

        $message->update([
            'wamm_message_id' => $wammMessageId,
            'status' => MessageStatus::SENT,
        ]);
    }
}
