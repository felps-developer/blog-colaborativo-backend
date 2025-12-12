<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'message' => 'Blog Colaborativo API',
        'version' => '1.0.0',
    ]);
});

