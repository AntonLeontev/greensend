<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class ApiController extends Controller
{
    public function scriptNodes(): JsonResponse
    {
        $nodes = [];

        foreach (config('setup.script_nodes') as $nodeClass) {
            $nodes[] = new $nodeClass;
        }

        return response()->json($nodes);
    }
}
