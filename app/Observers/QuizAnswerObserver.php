<?php

namespace App\Observers;

use App\Models\Quiz;
use App\Models\QuizAnswer;

use App\Models\Configuration;

class QuizAnswerObserver
{
    public function created(QuizAnswer $quizAnswer): void
    {
        $this->updateQuizStats($quizAnswer->quiz);
    }

    public function updated(QuizAnswer $quizAnswer): void
    {
        $this->updateQuizStats($quizAnswer->quiz);
    }
    
    public function deleted(QuizAnswer $quizAnswer): void
    {
        $this->updateQuizStats($quizAnswer->quiz);
    }
    
    private function updateQuizStats(Quiz $quiz): void
    {
        $quiz->load('answers');

        $correctCount = $quiz->answers->where('is_correct', true)->count();
        $wrongCount = $quiz->answers->where('is_correct', false)->count();

        $pointsPerQuestion = (int) Configuration::get('score_per_question', 10);
        
        $score = $correctCount * $pointsPerQuestion;

        $quiz->updateQuietly([
            'correct_count' => $correctCount,
            'wrong_count' => $wrongCount,
            'score' => $score,
        ]);
    }
}