<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Photo extends Model
{
    protected $fillable = [
        'filename',
        'original_filename',
        'disk',
        'path',
        'mime_type',
        'size',
        'taken_at',
        'camera_make',
        'camera_model',
        'latitude',
        'longitude',
        'orientation',
        'caption',
    ];

    protected $casts = [
        'taken_at' => 'datetime',
        'size' => 'integer',
        'latitude' => 'float',
        'longitude' => 'float',
        'orientation' => 'integer',
    ];

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'photo_tags');
    }

    public function url(): string
    {
        return \Storage::disk($this->disk)->url($this->path);
    }
}
