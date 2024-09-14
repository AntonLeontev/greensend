<?php

use App\Http\Controllers\AppController;
use Illuminate\Support\Facades\Route;

Route::get('/checkscript', function () {
    return view('welcome');
});

Route::post('/checkscript', [AppController::class, 'handle']);
