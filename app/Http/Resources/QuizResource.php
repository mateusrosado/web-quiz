<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'score' => $this->score,
            'duration' => $this->duration,
            'correct_count' => $this->correct_count,
            'wrong_count' => $this->wrong_count,
            'created_at' => $this->created_at->format('d/m/Y H:i'),
            'completed_at' => $this->completed_at ? $this->completed_at->format('d/m/Y H:i') : null,
            
            'user' => [
                'id' => $this->user->id ?? null,
                'name' => $this->user->name ?? 'Anônimo',
            ],
            
            'answers' => $this->whenLoaded('answers'),
        ];
    }
}
