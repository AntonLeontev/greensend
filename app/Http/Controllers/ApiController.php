<?php

namespace App\Http\Controllers;

use App\Enums\ConversationScriptCondition;
use Illuminate\Http\JsonResponse;

class ApiController extends Controller
{
    public function appData(): JsonResponse
    {
        $actions = [];

        foreach (config('setup.actions') as $actionClass) {
            $actions[] = new $actionClass;
        }

        $data = [
            'actions' => $actions,
            'conditions' => ConversationScriptCondition::toArray(),
        ];

        return response()->json($data);
    }
}
