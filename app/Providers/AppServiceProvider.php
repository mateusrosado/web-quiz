<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\QuizAnswer;
use App\Observers\QuizAnswerObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        QuizAnswer::observe(QuizAnswerObserver::class);
    }
}
