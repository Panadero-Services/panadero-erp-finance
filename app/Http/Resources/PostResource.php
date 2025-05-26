<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Post;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        //return parent::toArray($request);
        return [
            'id'=> $this->id,
            'user'=> $this->whenLoaded('user', fn () => UserResource::make($this->user)),
            'title'=> $this->title,
            'body'=> $this->body,
            'json'=> $this->json,
            'created_at'=> $this->created_at,
            'updated_at'=> $this->updated_at,
            'published'=> $this->published,
            'public'=> $this->public,
            'featured'=> $this->featured,
            'locked'=> $this->locked,
            'self'=> $this->self,
            'smart'=> $this->smart,
            'validation_rules' => Post::validationRules(),
            'form_fields' => Post::formFields()
        ];

    }
}
