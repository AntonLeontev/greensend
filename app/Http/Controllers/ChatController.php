<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index(Request $request): JsonResponse|View
    {
        if (! request()->ajax()) {
            return view('app');
        }

        $chats = Chat::query()
            ->when($request->has('channel'), fn ($q) => $q->where('channel_id', $request->channel))
            ->with(['channel', 'lastMessage'])
            ->orderBy('id', 'desc')
            ->paginate(20);

        return response()->json($chats);
    }
}
