<?php

namespace App\Services;

use App\Models\Question;
use Symfony\Component\HttpFoundation\StreamedResponse;

class QuestionExportService
{
    public function export(): StreamedResponse
    {
        $csvFileName = "questions_export.csv";
        
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$csvFileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            
            fputcsv($handle, ['Pergunta', 'Resposta Correta', 'Opcao Errada 1', 'Opcao Errada 2', 'Opcao Errada 3']);

            $questions = Question::with('options')->cursor();

            foreach ($questions as $q) {
                $correctOption = $q->options->firstWhere('is_correct', true);
                $wrongOptions = $q->options->where('is_correct', false);

                if (!$correctOption || $wrongOptions->count() < 3) continue;

                $row = [
                    $q->description,
                    $correctOption->text
                ];

                foreach ($wrongOptions as $wrong) {
                    $row[] = $wrong->text;
                }

                fputcsv($handle, $row);
            }

            fclose($handle);
        }, 200, $headers);
    }
}