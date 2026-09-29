<?php

namespace App\Livewire;

use App\Models\Photo;
use App\Models\Tag;
use Illuminate\Support\Str;
use Livewire\Component;

class EditPhoto extends Component
{
    public Photo $photo;

    public string $caption = '';
    public string $taken_at = '';
    public string $tagInput = '';
    public array $tags = [];

    public bool $saved = false;

    public function mount(Photo $photo): void
    {
        $this->photo   = $photo;
        $this->caption = $photo->caption ?? '';
        $this->taken_at = $photo->taken_at?->format('Y-m-d\TH:i') ?? '';
        $this->tags    = $photo->tags->pluck('name')->toArray();
    }

    public function addTag(): void
    {
        $name = trim($this->tagInput);

        if ($name === '' || in_array($name, $this->tags)) {
            $this->tagInput = '';
            return;
        }

        $this->tags[] = $name;
        $this->tagInput = '';
    }

    public function removeTag(string $name): void
    {
        $this->tags = array_values(array_filter($this->tags, fn($t) => $t !== $name));
    }

    public function save(): void
    {
        $this->validate([
            'caption' => 'nullable|string|max:1000',
            'taken_at' => 'nullable|date',
        ]);

        $this->photo->update([
            'caption'  => $this->caption ?: null,
            'taken_at' => $this->taken_at ?: null,
        ]);

        $tagIds = collect($this->tags)->map(function (string $name) {
            $slug = Str::slug($name);
            return Tag::firstOrCreate(['slug' => $slug], ['name' => $name])->id;
        });

        $this->photo->tags()->sync($tagIds);

        $this->saved = true;
    }

    public function render()
    {
        return view('livewire.edit-photo');
    }
}
