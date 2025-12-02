<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Configuration;

class DashboardController extends Controller
{
    public function insights()
    {
        $totalUsers = User::count();
        $totalQuizzes = Quiz::whereNotNull('completed_at')->count();
        $totalQuestions = Question::count();
        
        $limitPerQuiz = (int) (Configuration::where('key', 'quiz_question_limit')->value('value') ?? 10);
        $scorePerQuestion = (int) (Configuration::where('key', 'score_per_question')->value('value') ?? 10);
        
        $possibleCombinations = $this->calculateCombinations($totalQuestions, $limitPerQuiz);

        return response()->json([
            'total_users' => $totalUsers,
            'total_quizzes' => $totalQuizzes,
            'total_questions' => $totalQuestions,
            'possible_combinations' => $possibleCombinations,
            'quiz_limit' => $limitPerQuiz,
            'score_per_question' => $scorePerQuestion,
        ]);
    }

    public function quizzesHistory()
    {
        return Quiz::with('user')
            ->whereNotNull('completed_at')
            ->orderByDesc('created_at')
            ->paginate(20);
    }

    private function calculateCombinations($n, $k)
    {
        if ($n < $k) {
            return "0 (Perguntas insuficientes)";
        }
        if ($n == $k) {
            return "1 (Apenas um jogo possível)";
        }

        if ($k > $n / 2) {
            $k = $n - $k;
        }

        $result = 1;
        
        for ($i = 1; $i <= $k; $i++) {
            $result = $result * ($n - $i + 1) / $i;

            if ($result > 1000000000000) {
                return "Quase Infinitas (> 1 Tri)";
            }
        }

        return number_format($result, 0, ',', '.');
    }
}