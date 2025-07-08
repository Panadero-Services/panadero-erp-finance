<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class DynamicResource extends JsonResource
{
    protected $modelClass;
    protected $meta;

    public function __construct($resource, $modelClass = null, $meta = [])
    {
        parent::__construct($resource);
        $this->modelClass = $modelClass;
        $this->meta = $meta;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);
        
        // Add any loaded relationships
        if ($this->resource) {
            $relations = $this->resource->getRelations();
            foreach ($relations as $relation => $value) {
                if ($value !== null) {
                    // Try to find a resource for the related model
                    $relatedModelClass = get_class($value);
                    $relatedResourceClass = 'App\\Http\\Resources\\' . class_basename($relatedModelClass) . 'Resource';
                    
                    if (class_exists($relatedResourceClass)) {
                        $data[$relation] = new $relatedResourceClass($value);
                    } else {
                        // Use DynamicResource for related models too
                        $data[$relation] = new DynamicResource($value, $relatedModelClass);
                    }
                }
            }
        }
        
        return $data;
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function with($request)
    {
        // Only return meta if it was explicitly set
        return $this->meta ? ['meta' => $this->meta] : [];
    }

    /**
     * Create a new resource instance with meta data.
     *
     * @param  mixed  $resource
     * @param  string|null  $modelClass
     * @param  array  $meta
     * @return static
     */
    public static function makeWithMeta($resource, $modelClass = null, $meta = [])
    {
        return new static($resource, $modelClass, $meta);
    }
} 