<?php

namespace Src\ScriptNodes;

use App\Enums\MessageStatus;
use App\Models\Channel;
use App\Models\Chat;
use App\Models\Distribution;
use App\Models\Message;
use App\Services\Wamm\WammService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

final class SendWhatsAppTextMessage implements ShouldQueue
{
    use Queueable;

    public function __construct(private string $phone, private int $distributionId, private int $channelId) {}

    public static function title(): string
    {
        return 'Отправить текстовое сообщение WhatsApp';
    }

    public static function type(): string
    {
        return 'SendWhatsAppTextMessage';
    }

    public function handle(WammService $wamm): void
    {
        $channel = Channel::find($this->channelId);
        $distribution = Distribution::find($this->distributionId);

        $scriptRoot = $distribution->data->conversation[0];

        $chat = Chat::where('channel_id', $channel->id)
            ->where('phone', $this->phone)
            ->first();

        if ($chat) {
            $chat->update([
                'active_distribution_id' => $this->distributionId,
                'last_action_id' => $scriptRoot->id,
            ]);
        } else {
            $chat = Chat::create([
                'channel_id' => $channel->id,
                'phone' => $this->phone,
                'name' => $this->phone,
                'active_distribution_id' => $this->distributionId,
                'last_action_id' => $scriptRoot->id,
            ]);
        }

        $message = Message::create([
            'chat_id' => $chat->id,
            'text' => $scriptRoot->action->data->text,
            'is_incoming' => false,
            'status' => MessageStatus::INIT,
            'distribution_id' => $chat->active_distribution_id,
        ]);

        try {
            $wammMessageId = $wamm->sendMessage(
                phone: $this->phone,
                text: $scriptRoot->action->data->text,
                delay: $this->data->delay ?? null,
                token: $channel->token,
            );
        } catch (\Throwable $th) {
            $this->fail("Не удалось отправить сообщение в Wamm. Message id: {$message->id}. Причина: {$th->getMessage()}");
        }

        $message->update([
            'wamm_message_id' => $wammMessageId,
            'status' => MessageStatus::SENT,
        ]);
    }

    public static function serialize(): array
    {
        return [
            'type' => self::type(),
            'class' => self::class,
            'title' => self::title(),
        ];
    }
}
