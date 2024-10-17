<?php

use App\Http\Controllers\AppController;
use App\Services\Wamm\WammService;
use Illuminate\Support\Facades\Route;

Route::get('test', function (WammService $service) {
    $r = $service->sendMessage('79126510464', 'test message');

    dd($r);
});

Route::get('/checkscript', function () {
    return view('welcome');
});

Route::post('/checkscript', [AppController::class, 'handle']);
