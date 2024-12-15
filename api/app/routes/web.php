<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Guzzle サンプルその1(同期リクエスト)
Route::get('/sample_1/{id}', function (int $id) {
    return response()->json([
        'id' => $id,
    ]);
});

// Guzzle サンプルその2(同期リクエスト)
Route::get('/sample_2/{id}', function (int $id) {
    sleep(1);
    return response()->json([
        'id' => $id,
    ]);
});

// Guzzle サンプルその3(非同期リクエスト)
Route::get('/sample_3/{id}', function (int $id) {
    sleep(1);
    return response()->json([
        'id' => $id,
    ]);
});

// Guzzle サンプルその4(非同期リクエスト Pool)
Route::get('/sample_4/{id}', function (int $id) {
    return response()->json([
        'id' => $id,
    ]);
});

// Guzzle サンプルテスト
Route::middleware(['throttle:sample_test'])->get('/sample_test/{id}/{name}', function (int $id, string $name) {
    return response()->json([
        'id' => $id,
        'name' => $name,
    ]);
});
