<?php

namespace App\Livewire;

use App\Repositories\Contracts\PhotoRepositoryInterface;
use App\Repositories\Contracts\TagRepositoryInterface;
use App\Services\PhotoUploadService;
use Livewire\Attributes\Url;
use Livewire\Component;

class PhotoGallery extends Component
{
    #[Url]
    public string $search = '';

    #[Url]
    public string $tag = '';

    #[Url]
    public string $sortBy = 'taken_at';

    #[Url]
    public string $sortDir = 'desc';

    #[Url(as: 'photo')]
    public ?int $selected = null;

    public function delete(int $photoId, PhotoRepositoryInterface $repo, PhotoUploadService $service): void
    {
        $photo = $repo->find($photoId);

        if ($photo === null) {
            return;
        }

        $service->delete($photo);

        if ($this->selected === $photoId) {
            $this->selected = null;
        }
    }

    public function openPhoto(int $photoId): void
    {
        $this->selected = $photoId;
    }

    public function closePhoto(): void
    {
        $this->selected = null;
    }

    public function setSort(string $by): void
    {
        if ($this->sortBy === $by) {
            $this->sortDir = $this->sortDir === 'desc' ? 'asc' : 'desc';
        } else {
            $this->sortBy = $by;
            $this->sortDir = 'desc';
        }
    }

    public function filterByTag(string $slug): void
    {
        $this->tag = $slug;
        $this->selected = null;
    }

    public function clearTag(): void
    {
        $this->tag = '';
    }

    public function render(PhotoRepositoryInterface $repo, TagRepositoryInterface $tags)
    {
        $photos = $repo->filter([
            'search' => $this->search,
            'tag' => $this->tag,
            'sortBy' => $this->sortBy,
            'sortDir' => $this->sortDir,
        ]);

        $activeTag = $this->tag ? $tags->findBySlug($this->tag) : null;

        $selectedPhoto = null;
        $prevId = $nextId = null;

        if ($this->selected !== null) {
            $index = $photos->search(fn ($p) => $p->id === $this->selected);

            if ($index === false) {
                $this->selected = null;
            } else {
                $selectedPhoto = $photos[$index];
                $prevId = $photos[$index - 1]->id ?? null;
                $nextId = $photos[$index + 1]->id ?? null;
            }
        }

        return view('livewire.photo-gallery', compact('photos', 'activeTag', 'selectedPhoto', 'prevId', 'nextId'));
    }
}
