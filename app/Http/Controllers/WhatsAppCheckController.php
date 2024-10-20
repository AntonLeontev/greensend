<?php

namespace App\Http\Controllers;

use App\Services\Wamm\WammService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WhatsAppCheckController extends Controller
{
    public function __invoke(Request $request, WammService $wamm): JsonResponse
    {
        $request->validate([
            'phone' => ['required', 'starts_with:7'],
        ]);

        $result = $wamm->checkPhone($request->get('phone'));

        return response()->json(['result' => $result]);
    }
}
