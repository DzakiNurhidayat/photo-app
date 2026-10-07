<?php

namespace App\Repositories;

use App\Models\Tag;
use App\Repositories\Contracts\TagRepositoryInterface;
use Illuminate\Support\Str;

class EloquentTagRepository implements TagRepositoryInterface
{
    public function findBySlug(string $slug): ?Tag
    {
        return Tag::where('slug', $slug)->first();
    }

    public function firstOrCreateByName(string $name): Tag
    {
        return Tag::firstOrCreate(
            ['slug' => Str::slug($name)],
            ['name' => $name],
        );
    }
}
