<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    protected $fillable = [
        'user_id',
        'score',
        'questions_list',
        'correct_count',
        'wrong_count',
        'duration',
        'completed_at'
    ];

    protected $casts = [
        'questions_list' => 'array', 
        'completed_at' => 'datetime',
        'duration' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(QuizAnswer::class);
    }
}
