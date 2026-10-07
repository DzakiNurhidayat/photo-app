<?php

namespace App\Repositories\Contracts;

use App\Models\Photo;
use Carbon\Carbon;
use Illuminate\Support\Collection;

interface PhotoRepositoryInterface
{
    public function create(array $attributes): Photo;

    public function find(int $id): ?Photo;

    public function delete(Photo $photo): void;

    /**
     * Filter galeri. $filters: search, tag (slug), sortBy, sortDir.
     *
     * @return Collection<int, Photo>
     */
    public function filter(array $filters): Collection;

    /**
     * Foto pada bulan & hari yang sama dengan $date namun di tahun sebelumnya.
     *
     * @return Collection<int, Photo>
     */
    public function memoriesFor(Carbon $date): Collection;

    /**
     * Foto yang punya taken_at, terurut menaik (untuk event grouping).
     *
     * @return Collection<int, Photo>
     */
    public function datedOrdered(): Collection;

    public function countUndated(): int;
}
