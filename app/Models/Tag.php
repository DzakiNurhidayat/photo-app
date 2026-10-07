<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'tag_group_id'];

    public function photos(): BelongsToMany
    {
        return $this->belongsToMany(Photo::class, 'photo_tags');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(TagGroup::class, 'tag_group_id');
    }
}
