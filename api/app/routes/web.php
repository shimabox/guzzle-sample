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

// Guzzle サンプルその2(同期リクエスト)
Route::get('/sample_2/{id}', function ($id) {
    sleep(1);
    return response()->json([
        'id' => $id,
    ]);
});
