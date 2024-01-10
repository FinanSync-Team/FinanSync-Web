<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// Auth (Login & Register)
Route::group(['prefix' => 'auth'], function () {
    Route::post('/register', [\App\Http\Controllers\API\AuthController::class, 'register']);
    Route::post('/login', [\App\Http\Controllers\API\AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->delete('/logout', [\App\Http\Controllers\API\AuthController::class, 'logout']);
});

//profile
Route::group(['prefix' => 'profile', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', \App\Http\Controllers\API\ProfileController::class);
});

//finance routes
Route::group(['prefix' => 'finances', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/home', \App\Http\Controllers\API\HomeController::class);
    Route::get('/budgeting', \App\Http\Controllers\API\BudgetingController::class);
    Route::post('/budgeting/setup', [\App\Http\Controllers\API\FinanceController::class, 'setupMonthlyBudget']);
    Route::get('/', [\App\Http\Controllers\API\FinanceController::class, 'getAll']);
    Route::get('/{id}', [\App\Http\Controllers\API\FinanceController::class, 'getById']);
    Route::post('/', [\App\Http\Controllers\API\FinanceController::class, 'create']);
    Route::put('/{id}', [\App\Http\Controllers\API\FinanceController::class, 'update']);
    Route::delete('/{id}', [\App\Http\Controllers\API\FinanceController::class, 'delete']);
});