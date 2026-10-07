<?php

namespace App\Repositories\Contracts;

use App\Models\Tag;

interface TagRepositoryInterface
{
    public function findBySlug(string $slug): ?Tag;

    public function firstOrCreateByName(string $name): Tag;
}
