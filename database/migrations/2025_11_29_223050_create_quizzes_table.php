<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->json('questions_list')->nullable();

            $table->integer('score');
            $table->integer('correct_count');
            $table->integer('wrong_count');
            
            $table->float('duration');
            $table->timestamps();

            $table->timestamp('completed_at')->nullable();

            $table->index(['score', 'duration']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
