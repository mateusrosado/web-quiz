<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Quiz;

class UserController extends Controller
{
    public function stats(Request $request)
    {
        $user = $request->user();
        
        $quizzes = Quiz::where('user_id', $user->id)
            ->whereNotNull('completed_at')
            ->orderByDesc('created_at')
            ->get();

        $totalQuizzes = $quizzes->count();
        $totalScore = $quizzes->sum('score');
        
        $totalAnswers = $quizzes->sum(fn($q) => $q->correct_count + $q->wrong_count);
        $totalCorrect = $quizzes->sum('correct_count');
        
        $accuracy = $totalAnswers > 0 ? round(($totalCorrect / $totalAnswers) * 100, 1) : 0;

        return response()->json([
            'user' => $user,
            'stats' => [
                'total_quizzes' => $totalQuizzes,
                'total_xp' => $totalScore,
                'accuracy' => $accuracy
            ],
            'history' => $quizzes
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'nullable|string|min:6|confirmed'
        ]);

        $user->name = $request->name;
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json(['message' => 'Perfil atualizado!', 'user' => $user]);
    }
}
