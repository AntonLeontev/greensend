<?php

namespace App\Http\Controllers;

use App\Http\Requests\DistributionStoreRequest;
use App\Models\Distribution;
use Illuminate\Support\Carbon;

class DistributionController extends Controller
{
    public function index() {}

    public function store(DistributionStoreRequest $request)
    {
        if ($request->get('start_date') !== null && $request->get('start_time') !== null) {
            $startsAt = Carbon::parse($request->get('start_date').' '.$request->get('start_time'));
        } else {
            $startsAt = Carbon::now();
        }

        Distribution::create([
            'name' => $request->get('name'),
            'uploaded_file_id' => $request->get('uploaded_file_id'),
            'type' => $request->get('type'),
            'channel_id' => $request->get('channel_id'),
            'starts_at' => $startsAt,
            'data' => ['conversation' => json_decode($request->get('conversation'), true)],
        ]);
    }

    public function destroy() {}
}
