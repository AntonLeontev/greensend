<?php

namespace App\Http\Controllers;

use App\Http\Requests\DistributionStoreRequest;
use App\Models\Distribution;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class DistributionController extends Controller
{
    public function index(): JsonResponse|View
    {
        if (! request()->ajax()) {
            return view('app');
        }

        $distributions = Distribution::orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($distributions);
    }

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

    public function destroy(Distribution $distribution) {
		$distribution->delete();
	}
}
