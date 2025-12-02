<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController; 
use App\Http\Controllers\QuizController;
use App\Http\Controllers\UserController;

use App\Http\Controllers\QuestionController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SettingsController;

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
        Route::get('/admin/questions', [QuestionController::class, 'index']);
        Route::post('/admin/questions', [QuestionController::class, 'store']);
        Route::put('/admin/questions/{id}', [QuestionController::class, 'update']);
        Route::delete('/admin/questions/{id}', [QuestionController::class, 'destroy']);
        
        Route::post('/admin/questions/import', [QuestionController::class, 'import']);
        Route::get('/admin/questions/export', [QuestionController::class, 'export']);

        Route::get('/admin/settings', [SettingsController::class, 'index']);
        Route::post('/admin/settings', [SettingsController::class, 'update']);
        
        Route::get('/admin/insights', [DashboardController::class, 'insights']);
        Route::get('/admin/quizzes', [DashboardController::class, 'quizzesHistory']);
        
        Route::get('/admin/users', [UserManagementController::class, 'index']);
        Route::post('/admin/users/{id}/toggle-admin', [UserManagementController::class, 'toggleAdmin']);
        Route::delete('/admin/users/{id}', [UserManagementController::class, 'destroy']);
    });
});