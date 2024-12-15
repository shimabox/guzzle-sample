<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Guzzle サンプルその1(同期リクエスト)
Route::get('/sample_1/{id}', function ($id) {
    return response()->json([
        'id' => $id,
    ]);
});
