<?php

namespace App\Services;

use App\Models\Question;
use App\Models\Option;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class QuestionImportService
{
    public function import(string $filePath): int
    {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            throw new InvalidArgumentException("O arquivo não pode ser lido.");
        }

        $handle = fopen($filePath, "r");
        $count = 0;

        try {
            DB::beginTransaction();

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

                $count++;
            }

            DB::commit();
            
            return $count;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        } finally {
            if ($handle) fclose($handle);
        }
    }
}