<?php

use App\Http\Controllers\AppController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UploadedFileController;
use App\Http\Controllers\WhatsAppCheckController;
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
Route::get('user', function () {
    if (! auth()->check()) {
        abort(Response::HTTP_UNAUTHORIZED);
    }

    return response()->json(auth()->user());
});

Route::post('whats-app-check', WhatsAppCheckController::class);
Route::post('whats-app-check/{uploadedFile}', [WhatsAppCheckController::class, 'checkFile'])->name('whatsapp.check-file');

Route::middleware(['auth'])
    ->controller(UploadedFileController::class)
    ->group(function () {
        Route::get('uploaded-files', 'index')->name('uploaded-files.index');
        Route::post('uploaded-files', 'store')->name('uploaded-files.store');
        Route::delete('uploaded-files/{uploadedFile}', 'destroy')->name('uploaded-files.destroy');
        Route::post('uploaded-files/{uploadedFile}/archive', 'getArchive')->name('uploaded-files.archive');
    });

Route::view('reset-password', 'app')->name('password.reset');
Route::fallback(function () {
    return view('app');
});
