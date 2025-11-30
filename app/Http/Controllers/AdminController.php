<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Option;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function import(Request $request)
    {
        if (!$request->user()->is_admin) {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }

        $request->validate(['file' => 'required|file|mimes:csv,txt']);

        $file = $request->file('file');
        $handle = fopen($file->getPathname(), "r");

        DB::transaction(function () use ($handle) {
            $rowIndex = 0;

            while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $rowIndex++;

                if (count($row) < 5) continue;

                if ($rowIndex === 1 && strtolower($row[0]) === 'pergunta') continue;

                $question = Question::create(['description' => $row[0]]);

                $optionsData = [
                    ['text' => $row[1], 'is_correct' => true],
                    ['text' => $row[2], 'is_correct' => false],
                    ['text' => $row[3], 'is_correct' => false],
                    ['text' => $row[4], 'is_correct' => false],
                ];

                shuffle($optionsData);

                foreach ($optionsData as $opt) {
                    Option::create([
                        'question_id' => $question->id,
                        'text' => $opt['text'],
                        'is_correct' => $opt['is_correct']
                    ]);
                }
            }
        });

        fclose($handle);
        return response()->json(['message' => 'Importação concluída!']);
    }

    public function export(Request $request)
    {
        if (!$request->user()->is_admin) abort(403);

        $questions = Question::with('options')->get();
        
        $csvFileName = "questions_export.csv";
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$csvFileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use($questions) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['Pergunta', 'Resposta Correta', 'Opcao Errada 1', 'Opcao Errada 2', 'Opcao Errada 3']);

            foreach ($questions as $q) {
                $correctOption = $q->options->where('is_correct', true)->first();
                $wrongOptions = $q->options->where('is_correct', false);

                if (!$correctOption || $wrongOptions->count() < 3) continue;

                $row = [
                    $q->description,
                    $correctOption->text
                ];

                foreach($wrongOptions as $wrong) {
                    $row[] = $wrong->text;
                }

                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}