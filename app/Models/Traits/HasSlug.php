<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;

trait HasSlug
{
    protected static function bootHasSlug()
    {
        static::creating(function ($model) {
            if (!$model->slug) {
                $model->generateSlug();
            }
        });

        static::updating(function ($model) {
            if (!$model->slug) {
                $model->generateSlug();
            }
        });
    }

    protected function generateSlug()
    {
        $this->slug = Str::slug($this->title) . '-' . $this->id . '-' . time();
    }
} 