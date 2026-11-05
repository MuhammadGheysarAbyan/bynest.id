<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Portfolio extends Model
{
    protected $fillable = [
        'title', 'slug', 'category', 'description', 'thumbnail', 'project_url',
    ];

    protected $appends = ['thumbnail_url'];

    protected function thumbnailUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->thumbnail
                ? Storage::disk('public')->url($this->thumbnail)
                : null,
        );
    }
}