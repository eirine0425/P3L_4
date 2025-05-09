<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;


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
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:api',"multiRole:CS,Admin")->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::prefix('alamat')->group(function () {
    Route::get('/', [AlamatController::class, 'index']);
    Route::post('/', [AlamatController::class, 'store']);
    Route::get('/{id}', [AlamatController::class, 'show']);
    Route::put('/{id}', [AlamatController::class, 'update']);
    Route::delete('/{id}', [AlamatController::class, 'destroy']);
});

Route::prefix('organisasi')->group(function () {
    Route::get('/', [OrganisasiController::class, 'index']);
    Route::post('/', [OrganisasiController::class, 'store']);
    Route::get('/{id}', [OrganisasiController::class, 'show']);
    Route::put('/{id}', [OrganisasiController::class, 'update']);
    Route::delete('/{id}', [OrganisasiController::class, 'destroy']);
});

Route::prefix('pembeli')->group(function () {
    Route::get('/', [PembeliController::class, 'index']);
    Route::post('/', [PembeliController::class, 'store']);
    Route::get('/{id}', [PembeliController::class, 'show']);
    Route::put('/{id}', [PembeliController::class, 'update']);
    Route::delete('/{id}', [PembeliController::class, 'destroy']);
});

Route::prefix('penitip')->group(function () {
    Route::get('/', [PenitipController::class, 'index']);
    Route::post('/', [PenitipController::class, 'store']);
    Route::get('/{id}', [PenitipController::class, 'show']);
    Route::put('/{id}', [PenitipController::class, 'update']);
    Route::delete('/{id}', [PenitipController::class, 'destroy']);
});

Route::prefix('role')->group(function () {
    Route::get('/', [RoleController::class, 'index']);
    Route::post('/', [RoleController::class, 'store']);
    Route::get('/{id}', [RoleController::class, 'show']);
    Route::put('/{id}', [RoleController::class, 'update']);
    Route::delete('/{id}', [RoleController::class, 'destroy']);
});
