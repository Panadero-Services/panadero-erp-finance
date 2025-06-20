<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FutureResource extends JsonResource
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
            'item' => $this->item,
            'title' => $this->title,
            'description' => $this->description,
            'color' => $this->color,
            'version' => $this->version,
            'icon' => $this->icon,
            'status' => $this->status,
            'user' => new UserResource($this->whenLoaded('user')),
            'project' => new ProjectResource($this->whenLoaded('project')),
            'json' => $this->json,
            'links' => $this->links,
            'is_active' => $this->is_active,
            'is_locked' => $this->is_locked,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'validation_rules' => $this->resource::validationRules(),
            'form_fields' => $this->resource::formFields(),
            'links_table' => $this->resource::linksTable(),
            'searchable_columns' => (new $this->resource)->getSearchableColumns(),
            'table_columns' => $this->resource::getTableColumns(),
            'status_mapping' => $this->resource::getStatusMapping(),
        ];
    }
} 