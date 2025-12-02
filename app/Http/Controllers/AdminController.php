<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\User;
use App\Models\Quiz;
use App\Models\Configuration;
use App\Models\Option;
use Illuminate\Support\Facades\DB;

// Mantenha os services que você já usa para import/export
use App\Services\QuestionImportService;
use App\Services\QuestionExportService;

class AdminController extends Controller
{
    public function import(Request $request, QuestionImportService $importer)
    {
        $request->validate(['file' => 'required|file|mimes:csv,txt']);

        try {
            $count = $importer->import($request->file('file')->getPathname());

            return response()->json([
                'message' => "Importação concluída com sucesso!",
                'questions_imported' => $count
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao processar o arquivo.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function export(QuestionExportService $exporter)
    {
        return $exporter->export();
    }

    public function insights()
    {
        $totalUsers = User::count();
        $totalQuizzes = Quiz::whereNotNull('completed_at')->count();
        $totalQuestions = Question::count();
        
        $limitPerQuiz = (int) Configuration::get('quiz_question_limit', 10);
        
        $possibleCombinations = $this->calculateCombinations($totalQuestions, $limitPerQuiz);

        return response()->json([
            'total_users' => $totalUsers,
            'total_quizzes' => $totalQuizzes,
            'total_questions' => $totalQuestions,
            'possible_combinations' => $possibleCombinations,
            'quiz_limit' => $limitPerQuiz,
            'score_per_question' => (int) Configuration::get('score_per_question', 10),
        ]);
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

    public function getSettings()
    {
        return response()->json(Configuration::all()->pluck('value', 'key'));
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'quiz_question_limit' => 'required|integer|min:1',
            'score_per_question'  => 'required|integer|min:1',
        ]);

        foreach ($data as $key => $value) {
            Configuration::set($key, $value);
        }

        return response()->json(['message' => 'Configurações atualizadas com sucesso!']);
    }

    public function listUsers()
    {
        return User::orderBy('id', 'desc')->paginate(20);
    }

    public function toggleAdmin(Request $request, $id)
    {
        if ($request->user()->id == $id) {
            return response()->json(['message' => 'Você não pode alterar seu próprio status.'], 403);
        }

        $user = User::findOrFail($id);
        $user->is_admin = !$user->is_admin;
        $user->save();

        return response()->json([
            'message' => 'Status de administrador atualizado.', 
            'is_admin' => $user->is_admin
        ]);
    }

    public function deleteUser(Request $request, $id)
    {
        if ($request->user()->id == $id) {
            return response()->json(['message' => 'Você não pode se excluir.'], 403);
        }

        User::destroy($id);
        return response()->json(['message' => 'Usuário excluído com sucesso.']);
    }

    public function quizzesHistory()
    {
        return Quiz::with('user')
            ->whereNotNull('completed_at')
            ->orderByDesc('created_at')
            ->paginate(20);
    }

    public function listQuestions()
    {
        return Question::with('options')->orderByDesc('id')->paginate(15);
    }

    public function storeQuestion(Request $request)
    {
        $request->validate([
            'description' => 'required|string',
            'options' => 'required|array|min:4|max:4',
            'options.*.text' => 'required|string',
            'options.*.is_correct' => 'required|boolean'
        ]);

        DB::transaction(function() use ($request) {
            $question = Question::create(['description' => $request->description]);

            foreach($request->options as $opt) {
                Option::create([
                    'question_id' => $question->id,
                    'text' => $opt['text'],
                    'is_correct' => $opt['is_correct']
                ]);
            }
        });

        return response()->json(['message' => 'Pergunta criada com sucesso!']);
    }

    public function updateQuestion(Request $request, $id)
    {
        $request->validate([
            'description' => 'required|string',
            'options' => 'required|array|min:2',
            'options.*.id' => 'nullable|integer',
            'options.*.text' => 'required|string',
            'options.*.is_correct' => 'required|boolean'
        ]);

        $question = Question::findOrFail($id);

        DB::transaction(function() use ($request, $question) {
            $question->update(['description' => $request->description]);

            $optionIdsToKeep = [];

            foreach($request->options as $optData) {
                if (isset($optData['id'])) {
                    $option = Option::where('question_id', $question->id)->find($optData['id']);
                    if ($option) {
                        $option->update([
                            'text' => $optData['text'],
                            'is_correct' => $optData['is_correct']
                        ]);
                        $optionIdsToKeep[] = $option->id;
                    }
                } else {
                    $newOption = Option::create([
                        'question_id' => $question->id,
                        'text' => $optData['text'],
                        'is_correct' => $optData['is_correct']
                    ]);
                    $optionIdsToKeep[] = $newOption->id;
                }
            }

            Option::where('question_id', $question->id)
                  ->whereNotIn('id', $optionIdsToKeep)
                  ->delete();
        });

        return response()->json(['message' => 'Pergunta atualizada com sucesso!']);
    }

    public function deleteQuestion($id)
    {
        $question = Question::findOrFail($id);
        
        $question->delete();

        return response()->json(['message' => 'Pergunta excluída com sucesso.']);
    }
}