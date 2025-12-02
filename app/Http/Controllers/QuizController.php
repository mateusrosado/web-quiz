<?php

namespace App\Http\Controllers;

use App\Models\Option;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizAnswer;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Resources\QuestionResource;
use App\Http\Resources\QuizResource;

use App\Models\Configuration;

class QuizController extends Controller
{
    public function start(Request $request)
    {
        $limit = (int) Configuration::get('quiz_question_limit', 10);

        $allIds = Question::pluck('id');

        if ($allIds->isEmpty()) {
            return response()->json(['message' => 'Nenhuma pergunta disponível.'], 400);
        }

        $randomIds = $allIds->count() <= $limit 
            ? $allIds 
            : $allIds->random($limit);

        $questions = Question::with(['options'])
            ->whereIn('id', $randomIds)
            ->get();

        $questions = $questions->shuffle();

        $questionIds = $questions->pluck('id')->toArray();

        $quiz = Quiz::create([
            'user_id' => $request->user()->id,
            'score' => 0,
            'correct_count' => 0,
            'wrong_count' => 0,
            'duration' => 0,
            'questions_list' => $questionIds
        ]);

        return response()->json([
            'quiz' => new QuizResource($quiz),
            'questions' => QuestionResource::collection($questions)
        ]);
    }

    public function submitAnswer(Request $request)
    {
        $request->validate([
            'quiz_id' => 'required|exists:quizzes,id',
            'question_id' => 'required|exists:questions,id',
            'option_id' => 'required|exists:options,id',
        ]);

        $quiz = Quiz::where('id', $request->quiz_id)
                    ->where('user_id', $request->user()->id)
                    ->firstOrFail(); 

        $allowedQuestions = $quiz->questions_list;

        if (is_null($allowedQuestions) || !in_array($request->question_id, $allowedQuestions)) {
            return response()->json([
                'message' => 'Ação não permitida: Esta pergunta não pertence a este jogo ou o jogo é inválido.'
            ], 403);
        }

        $alreadyAnswered = QuizAnswer::where('quiz_id', $quiz->id)
                                        ->where('question_id', $request->question_id)
                                        ->exists();

        if ($alreadyAnswered) {
            return response()->json(['message' => 'Você já respondeu esta pergunta.'], 409);
        }

        $option = Option::find($request->option_id);
        $isCorrect = $option->is_correct;

        QuizAnswer::create([
            'quiz_id' => $quiz->id,
            'question_id' => $request->question_id,
            'option_id' => $request->option_id,
            'is_correct' => $isCorrect
        ]);

        return response()->json([
            'is_correct' => $isCorrect,
            'correct_option_id' => $isCorrect ? null : Option::where('question_id', $request->question_id)->where('is_correct', true)->first()->id
        ]);
    }

    public function finish(Request $request, $id)
    {
        $quiz = Quiz::where('id', $id)
                    ->where('user_id', $request->user()->id)
                    ->firstOrFail();

        $completedAt = now();

        $duration = $quiz->created_at->diffInSeconds($completedAt);

        $quiz->update([
            'duration' => $duration,
            'completed_at' => $completedAt
        ]);
        
        return response()->json([
            'message' => 'Quiz finalizado', 
            'redirect_to' => "/quiz/{$id}"
        ]);
    }

    public function ranking()
    {
        $topUsers = \App\Models\User::withSum('quizzes', 'score')
            ->orderByDesc('quizzes_sum_score')
            ->take(3)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'total_xp' => $user->quizzes_sum_score ?? 0,
                    'level' => floor(($user->quizzes_sum_score ?? 0) / 100) + 1 
                ];
            });

        $topQuizzes = Quiz::with('user:id,name')
            ->whereNotNull('completed_at')
            ->orderByDesc('score')
            ->orderBy('duration')
            ->take(10)
            ->get();

        return response()->json([
            'top_users' => $topUsers,
            'top_quizzes' => QuizResource::collection($topQuizzes)
        ]);
    }

    public function show(string $id, Request $request)
    {
        $quiz = Quiz::with(['answers.question', 'answers.option'])->findOrFail($id);

        if ($request->user()->cannot('view', $quiz)) {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }

        return new QuizResource($quiz);
    }
}
