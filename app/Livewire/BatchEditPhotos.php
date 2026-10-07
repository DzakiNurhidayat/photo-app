<?php

namespace App\Livewire;

use App\Models\Photo;
use App\Repositories\Contracts\TagRepositoryInterface;
use Livewire\Component;

class BatchEditPhotos extends Component
{
    public array $data = [];

    public function mount(string $ids): void
    {
        $photos = Photo::whereIn('id', explode(',', $ids))->get();

        foreach ($photos as $photo) {
            $this->data[$photo->id] = [
                'photo' => $photo,
                'caption' => $photo->caption ?? '',
                'taken_at' => $photo->taken_at?->format('Y-m-d\TH:i') ?? '',
                'tagInput' => '',
                'tags' => $photo->tags->pluck('name')->toArray(),
                'saved' => false,
            ];
        }
    }

    public function addTag(int $photoId): void
    {
        $name = trim($this->data[$photoId]['tagInput']);

        if ($name === '' || in_array($name, $this->data[$photoId]['tags'])) {
            $this->data[$photoId]['tagInput'] = '';

            return;
        }

        $this->data[$photoId]['tags'][] = $name;
        $this->data[$photoId]['tagInput'] = '';
    }

    public function removeTag(int $photoId, string $name): void
    {
        $this->data[$photoId]['tags'] = array_values(
            array_filter($this->data[$photoId]['tags'], fn ($t) => $t !== $name)
        );
    }

    public function savePhoto(int $photoId, TagRepositoryInterface $tags): void
    {
        $this->validate([
            "data.$photoId.caption" => 'nullable|string|max:1000',
            "data.$photoId.taken_at" => 'nullable|date',
        ]);

        $photo = Photo::findOrFail($photoId);

        $photo->update([
            'caption' => $this->data[$photoId]['caption'] ?: null,
            'taken_at' => $this->data[$photoId]['taken_at'] ?: null,
        ]);

        $tagIds = collect($this->data[$photoId]['tags'])
            ->map(fn (string $name) => $tags->firstOrCreateByName($name)->id);

        $photo->tags()->sync($tagIds);

        $this->data[$photoId]['saved'] = true;
    }

    public function saveAll(TagRepositoryInterface $tags): void
    {
        foreach (array_keys($this->data) as $photoId) {
            $this->savePhoto($photoId, $tags);
        }

        $this->redirect(route('photos.index'));
    }

    public function render()
    {
        return view('livewire.batch-edit-photos');
    }
}
