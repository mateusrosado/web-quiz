<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController; 
use App\Http\Controllers\QuizController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/ranking', [QuizController::class, 'ranking']);

Route::middleware('auth:sanctum')->group(function () {
    
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::get('/user/stats', [UserController::class, 'stats']);
    Route::post('/user/update', [UserController::class, 'update']);
    
    Route::get('/quiz/start', [QuizController::class, 'start']);
    Route::post('/quiz/answer', [QuizController::class, 'submitAnswer']);
    Route::post('/quiz/finish/{id}', [QuizController::class, 'finish']);
    Route::get('/quiz/{id}', [QuizController::class, 'show']);
    
    Route::middleware('admin')->group(function () {
        Route::post('/admin/questions/import', [AdminController::class, 'import']);
        Route::get('/admin/questions/export', [AdminController::class, 'export']);

        Route::get('/admin/settings', [AdminController::class, 'getSettings']);
        Route::post('/admin/settings', [AdminController::class, 'updateSettings']);

        Route::get('/admin/insights', [AdminController::class, 'insights']);
        
        Route::get('/admin/users', [AdminController::class, 'listUsers']);
        Route::post('/admin/users/{id}/toggle-admin', [AdminController::class, 'toggleAdmin']);
        Route::delete('/admin/users/{id}', [AdminController::class, 'deleteUser']);
        
        Route::get('/admin/quizzes', [AdminController::class, 'quizzesHistory']);
        
        Route::get('/admin/questions', [AdminController::class, 'listQuestions']);
        Route::post('/admin/questions', [AdminController::class, 'storeQuestion']);
        Route::put('/admin/questions/{id}', [AdminController::class, 'updateQuestion']);
        Route::delete('/admin/questions/{id}', [AdminController::class, 'deleteQuestion']);
    });
});