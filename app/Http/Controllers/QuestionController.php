<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Option;
use Illuminate\Support\Facades\DB;
use App\Services\QuestionImportService;
use App\Services\QuestionExportService;

class QuestionController extends Controller
{
    public function index()
    {
        return Question::with('options')->orderByDesc('id')->paginate(15);
    }

    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string',
            'options' => [
                'required', 'array', 'min:4', 'max:4',
                function ($attribute, $value, $fail) {
                    if (!collect($value)->contains('is_correct', true)) {
                        $fail('É necessário preencher a Resposta Correta.');
                    }
                }
            ],
            'options.*.text' => 'required|string',
            'options.*.is_correct' => 'required|boolean'
        ]);

        DB::transaction(function() use ($request) {
            $question = Question::create(['description' => $request->description]);

            $optionsData = $request->options;
            
            shuffle($optionsData); 

            foreach($optionsData as $opt) {
                Option::create([
                    'question_id' => $question->id,
                    'text' => $opt['text'],
                    'is_correct' => $opt['is_correct']
                ]);
            }
        });

        return response()->json(['message' => 'Pergunta criada com sucesso!']);
    }

    public function update(Request $request, $id)
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

    public function destroy($id)
    {
        $question = Question::findOrFail($id);
        $question->delete();

        return response()->json(['message' => 'Pergunta excluída com sucesso.']);
    }

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
}