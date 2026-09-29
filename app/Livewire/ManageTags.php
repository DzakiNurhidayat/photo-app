<?php

namespace App\Livewire;

use App\Models\Tag;
use App\Models\TagGroup;
use Illuminate\Support\Str;
use Livewire\Component;

class ManageTags extends Component
{
    // Tag editing
    public ?int $editingId = null;
    public string $editingName = '';

    // Group editing
    public ?int $editingGroupId = null;
    public string $editingGroupName = '';

    // New group form
    public bool $showNewGroupForm = false;
    public string $newGroupName = '';

    // --- Tag actions ---

    public function startEdit(int $id): void
    {
        $this->editingId   = $id;
        $this->editingName = Tag::find($id)?->name ?? '';
    }

    public function cancelEdit(): void
    {
        $this->editingId   = null;
        $this->editingName = '';
    }

    public function saveEdit(): void
    {
        $this->validate(['editingName' => 'required|string|max:100']);

        $tag = Tag::findOrFail($this->editingId);
        $tag->update([
            'name' => trim($this->editingName),
            'slug' => Str::slug(trim($this->editingName)),
        ]);

        $this->editingId   = null;
        $this->editingName = '';
    }

    public function delete(int $id): void
    {
        $tag = Tag::findOrFail($id);
        $tag->photos()->detach();
        $tag->delete();
    }

    public function assignGroup(int $tagId, string $groupId): void
    {
        Tag::findOrFail($tagId)->update([
            'tag_group_id' => $groupId === '' ? null : (int) $groupId,
        ]);
    }

    // --- Group actions ---

    public function startEditGroup(int $id): void
    {
        $this->editingGroupId   = $id;
        $this->editingGroupName = TagGroup::find($id)?->name ?? '';
    }

    public function cancelEditGroup(): void
    {
        $this->editingGroupId   = null;
        $this->editingGroupName = '';
    }

    public function saveEditGroup(): void
    {
        $this->validate(['editingGroupName' => 'required|string|max:100']);

        $group = TagGroup::findOrFail($this->editingGroupId);
        $group->update([
            'name' => trim($this->editingGroupName),
            'slug' => Str::slug(trim($this->editingGroupName)),
        ]);

        $this->editingGroupId   = null;
        $this->editingGroupName = '';
    }

    public function deleteGroup(int $id): void
    {
        $group = TagGroup::findOrFail($id);
        // Unassign tags before deleting group
        $group->tags()->update(['tag_group_id' => null]);
        $group->delete();
    }

    public function createGroup(): void
    {
        $this->validate(['newGroupName' => 'required|string|max:100']);

        TagGroup::create([
            'name' => trim($this->newGroupName),
            'slug' => Str::slug(trim($this->newGroupName)),
        ]);

        $this->newGroupName    = '';
        $this->showNewGroupForm = false;
    }

    public function render()
    {
        return view('livewire.manage-tags', [
            'groups' => TagGroup::withCount('tags')->orderBy('sort_order')->orderBy('name')->get(),
            'tags'   => Tag::withCount('photos')->with('group')->orderBy('name')->get(),
        ]);
    }
}
