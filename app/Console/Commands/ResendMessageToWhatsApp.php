<?php

namespace App\Console\Commands;

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
        $chat = $message->chat;
        $channel = $chat->channel;

        $wamm->sendMessage(
            phone: $chat->phone,
            text: $message->text,
            delay: 10,
            token: $channel->token,
        );
    }
}
