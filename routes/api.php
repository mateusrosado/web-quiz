<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController; 
use App\Http\Controllers\QuizController;
use App\Http\Controllers\AdminController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/ranking', [QuizController::class, 'ranking']);

Route::middleware('auth:sanctum')->group(function () {
    
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::get('/quiz/start', [QuizController::class, 'start']);
    Route::post('/quiz/answer', [QuizController::class, 'submitAnswer']);
    Route::post('/quiz/finish/{id}', [QuizController::class, 'finish']);
    Route::get('/quiz/{id}', [QuizController::class, 'show']);
    
    Route::middleware('admin')->group(function () {
        Route::post('/admin/questions/import', [AdminController::class, 'import']);
        Route::get('/admin/questions/export', [AdminController::class, 'export']);
    });
});