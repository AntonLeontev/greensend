<?php

use App\Http\Controllers\AppController;
use App\Services\Wamm\WammService;
use Illuminate\Support\Facades\Route;

if (config('app.url') === 'http://127.0.0.1:8000') {
    Route::get('test', function (WammService $service) {
        $r = $service->channelList();

        dd($r);
    });
}

Route::get('/', function () {
    return view('app');
});

Route::get('/checkscript', function () {
    return view('welcome');
});

Route::post('/checkscript', [AppController::class, 'handle']);

Route::get('/checkscript2', function () {
    return view('welcome');
});

Route::post('/checkscript2', [AppController::class, 'handle2']);
