<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TagGroup extends Model
{
    protected $fillable = ['name', 'slug', 'sort_order'];

    public function tags(): HasMany
    {
        return $this->hasMany(Tag::class);
    }
}
