<?php

namespace App\Http\Controllers;

use App\DTO\WammWhatsAppMessageDTO;
use App\Enums\MessageStatus;
use App\Jobs\HandleIncomeWhatsAppMessage;
use App\Models\Message;
use Illuminate\Http\Request;

class WammController extends Controller
{
    public function __invoke(Request $request)
    {
        if ($request->json('tip') === 'msg_state') {
            $message = Message::where('wamm_message_id', $request->json('msg_data.msg_id'))->first();

            $newStatus = MessageStatus::from($request->json('msg_data.state'));

            if ($message->status !== $newStatus) {
                $message->update(['status' => $newStatus]);
            }
        }

        if ($request->json('tip') === 'msg') {
            dispatch(new HandleIncomeWhatsAppMessage(WammWhatsAppMessageDTO::fromWebhookRequest($request->json('msg_data'))));
        }
    }
}
