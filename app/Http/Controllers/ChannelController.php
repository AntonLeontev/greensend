<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChannelCreateRequest;
use App\Models\Channel;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

class ChannelController extends Controller
{
    public function index(): JsonResponse|View
    {
        return request()->ajax()
            ? response()->json(Channel::all())
            : view('app');
    }

    public function store(ChannelCreateRequest $request): JsonResponse
    {
        if (Channel::where('is_main', true)->exists()) {
            $channel = Channel::create($request->validated());
        } else {
            $channel = Channel::create(array_merge($request->validated(), ['is_main' => true]));
        }

        return response()->json($channel);
    }

    public function destroy(Channel $channel): void
    {
        $channel->delete();
    }
}
