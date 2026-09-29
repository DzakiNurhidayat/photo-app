<?php

namespace App\Livewire;

use App\Models\Photo;
use App\Models\Tag;
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

    public function delete(int $photoId, PhotoUploadService $service): void
    {
        $photo = Photo::findOrFail($photoId);
        $service->delete($photo);
    }

    public function setSort(string $by): void
    {
        if ($this->sortBy === $by) {
            $this->sortDir = $this->sortDir === 'desc' ? 'asc' : 'desc';
        } else {
            $this->sortBy  = $by;
            $this->sortDir = 'desc';
        }
    }

    public function clearTag(): void
    {
        $this->tag = '';
    }

    public function render()
    {
        $query = Photo::with('tags');

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('original_filename', 'ilike', '%' . $this->search . '%')
                  ->orWhere('caption', 'ilike', '%' . $this->search . '%');
            });
        }

        if ($this->tag !== '') {
            $query->whereHas('tags', fn($q) => $q->where('slug', $this->tag));
        }

        $allowed = ['taken_at', 'created_at', 'original_filename'];
        $sortBy  = in_array($this->sortBy, $allowed) ? $this->sortBy : 'taken_at';
        $sortDir = $this->sortDir === 'asc' ? 'asc' : 'desc';

        $photos = $query->orderBy($sortBy, $sortDir)->get();

        $activeTag = $this->tag ? Tag::where('slug', $this->tag)->first() : null;

        return view('livewire.photo-gallery', compact('photos', 'activeTag'));
    }
}
