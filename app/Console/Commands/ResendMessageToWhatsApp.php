<?php

namespace App\Console\Commands;

use App\Enums\MessageStatus;
use App\Models\Message;
use App\Services\Wamm\WammService;
use Illuminate\Console\Command;

class ResendMessageToWhatsApp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:resend-wa {id?}';

    public function handle(WammService $wamm)
    {
        $id = $this->argument('id') ?? $this->ask('Message id');

        $message = Message::find($id);

        if ($message === null) {
            $this->error("Сообщение с id $id не найдено");

            return;
        }

        if ($message->is_incoming) {
            $this->error('Это входящее сообшение. Нельзя отправить повторно');

            return;
        }

        $chat = $message->chat;
        $channel = $chat->channel;

        $wammMessageId = $wamm->sendMessage(
            phone: $chat->phone,
            text: $message->text,
            delay: 10,
            token: $channel->token,
        );

        $message->update([
            'wamm_message_id' => $wammMessageId,
            'status' => MessageStatus::SENT,
        ]);
    }
}
