<?php

namespace App\Http\Controllers;

use App\Models\Option;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    public function start(Request $request)
    {
        $quiz = Quiz::create([
            'user_id' => $request->user()->id,
            'score' => 0,
            'correct_count' => 0,
            'wrong_count' => 0,
            'duration' => 0
        ]);

        $questions = Question::with('options')->inRandomOrder()->take(10)->get();

        return response()->json([
            'quiz_id' => $quiz->id,
            'questions' => $questions
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

        $option = Option::find($request->option_id);
        $isCorrect = $option->is_correct;

        QuizAnswer::create([
            'quiz_id' => $quiz->id,
            'question_id' => $request->question_id,
            'option_id' => $request->option_id,
            'is_correct' => $isCorrect
        ]);

        if ($isCorrect) {
            $quiz->increment('score', 10);
            $quiz->increment('correct_count');
        } else {
            $quiz->increment('wrong_count');
        }

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

        $quiz->update(['duration' => $request->duration]);
        
        return response()->json([
            'message' => 'Quiz finalizado', 
            'redirect_to' => "/quiz/{$id}"
        ]);
    }

    public function ranking()
    {
        $ranking = Quiz::with('user:id,name')
            ->orderByDesc('score')
            ->orderBy('duration')
            ->take(10)
            ->get();

        return response()->json($ranking);
    }

    public function show(string $id, Request $request)
    {
        $quiz = Quiz::with(['answers.question', 'answers.option'])->findOrFail($id);

        if ($request->user()->cannot('view', $quiz)) {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }

        return response()->json($quiz);
    }
}
