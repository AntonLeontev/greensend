<?php

use App\Http\Controllers\AppController;
use App\Http\Controllers\AuthController;
use App\Services\Wamm\WammService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

if (config('app.url') === 'http://127.0.0.1:8000') {
    Route::get('test', function (WammService $service) {
        $r = $service->channelList();

        dd($r);
    });
}

Route::get('/checkscript', function () {
    return view('welcome');
})->name('script');

Route::post('/checkscript', [AppController::class, 'handle']);

Route::get('/checkscript2', function () {
    return view('welcome');
})->name('script2');

Route::post('/checkscript2', [AppController::class, 'handle2']);

Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot-password');
Route::get('user', function () {
    if (! auth()->check()) {
        abort(Response::HTTP_UNAUTHORIZED);
    }

    return response()->json(auth()->user());
});

Route::get('password-reset', function () {})->name('password.reset');

Route::fallback(function () {
    return view('app');
});
