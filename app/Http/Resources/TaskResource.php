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
            'user_id' => $this->user_id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'term' => $this->term,
            'time' => $this->time,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'user' => $this->whenLoaded('user', function () {
                return new UserResource($this->user);
            })
        ];
    }
}
