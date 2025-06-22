<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Project;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=> $this->id,
            'user'=> $this->whenLoaded('user', fn () => UserResource::make($this->user)),
            'title'=> $this->title,
            'description'=> $this->description,
            'color'=> $this->color,
            'status'=> $this->status,
            'json'=> $this->json,
            'links'=> $this->links,
            'user_id'=> $this->user_id,
            'is_active'=> $this->is_active,
            'created_at'=> $this->created_at,
            'updated_at'=> $this->updated_at,
        ];
    }
}
