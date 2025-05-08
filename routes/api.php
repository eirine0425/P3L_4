<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
Route::get('/users', [UserController::class, 'index']);

// Route::prefix('users')->group(function () {
//     //http://127.0.0.1:8000/api/usersy

Route::post('/users', [UserController::class, 'store']);
//     Route::get('/{id}', [UserController::class, 'show']);
//     Route::put('/{id}', [UserController::class, 'update']);
//     Route::delete('/{id}', [UserController::class, 'destroy']);
// });