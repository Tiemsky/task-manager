<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'priority' => $this->priority,
            'status' => $this->status,
            'due_date' => $this->due_date?->format('Y-m-d'),
            'completed_at' => $this->completed_at?->format('Y-m-d H:i:s'),
            'is_overdue' => $this->isOverdue(),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'user' => [
                'id' => $this->user_id,
                'name' => $this->whenLoaded('user', fn () => $this->user->name),
            ],
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
