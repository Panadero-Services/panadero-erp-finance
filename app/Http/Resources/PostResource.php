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
            //'user_id'=> $this->user_id,
            'user'=> $this->whenLoaded('user', fn () => UserResource::make($this->user)),
            'title'=> $this->title,
            'body'=> $this->body,
            'json'=> $this->json,
            'links'=> $this->links,
            'created_at'=> $this->created_at,
            'updated_at'=> $this->updated_at,
            'is_published'=> $this->is_published==1,
            'is_public'=> $this->is_public==1,
            'is_featured'=> $this->is_featured==1,
            'is_locked'=> $this->is_locked==1,
            'is_self'=> $this->is_self==1,
            'is_smart'=> $this->is_smart==1,
            'is_active'=> $this->is_active==1,
            'is_archived'=> $this->is_archived==1,
            'validation_rules' => Post::validationRules(),
            'form_fields' => Post::formFields(),
            'links_table' => Post::linksTable()
        ];

    }
}
