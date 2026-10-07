<?php

namespace App\Repositories;

use App\Models\Photo;
use App\Repositories\Contracts\PhotoRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class EloquentPhotoRepository implements PhotoRepositoryInterface
{
    private const SORTABLE = ['taken_at', 'created_at', 'original_filename'];

    public function create(array $attributes): Photo
    {
        return Photo::create($attributes);
    }

    public function find(int $id): ?Photo
    {
        return Photo::with('tags')->find($id);
    }

    public function delete(Photo $photo): void
    {
        $photo->delete();
    }

    public function filter(array $filters): Collection
    {
        $query = Photo::with('tags');

        $search = trim($filters['search'] ?? '');
        if ($search !== '') {
            $term = '%'.mb_strtolower($search).'%';
            $query->where(function ($q) use ($term) {
                $q->whereRaw('lower(original_filename) like ?', [$term])
                    ->orWhereRaw('lower(caption) like ?', [$term]);
            });
        }

        $tag = $filters['tag'] ?? '';
        if ($tag !== '') {
            $query->whereHas('tags', fn ($q) => $q->where('slug', $tag));
        }

        $sortBy = in_array($filters['sortBy'] ?? null, self::SORTABLE, true) ? $filters['sortBy'] : 'taken_at';
        $sortDir = ($filters['sortDir'] ?? '') === 'asc' ? 'asc' : 'desc';

        return $query->orderBy($sortBy, $sortDir)->get();
    }

    public function memoriesFor(Carbon $date): Collection
    {
        return Photo::with('tags')
            ->whereNotNull('taken_at')
            ->whereMonth('taken_at', $date->month)
            ->whereDay('taken_at', $date->day)
            ->whereYear('taken_at', '<', $date->year)
            ->orderBy('taken_at', 'desc')
            ->get();
    }

    public function datedOrdered(): Collection
    {
        return Photo::with('tags')
            ->whereNotNull('taken_at')
            ->orderBy('taken_at')
            ->get();
    }

    public function countUndated(): int
    {
        return Photo::whereNull('taken_at')->count();
    }
}
