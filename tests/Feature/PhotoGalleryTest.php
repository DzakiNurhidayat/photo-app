<?php

namespace Tests\Feature;

use App\Livewire\PhotoGallery;
use App\Models\Photo;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class PhotoGalleryTest extends TestCase
{
    use RefreshDatabase;

    public function test_gallery_lists_photos(): void
    {
        Photo::factory()->count(3)->create();

        Livewire::test(PhotoGallery::class)
            ->assertViewHas('photos', fn ($photos) => $photos->count() === 3);
    }

    public function test_search_filters_by_filename_case_insensitive(): void
    {
        Photo::factory()->create(['original_filename' => 'Liburan-Bali.jpg']);
        Photo::factory()->create(['original_filename' => 'kantor.jpg']);

        Livewire::test(PhotoGallery::class)
            ->set('search', 'bali')
            ->assertViewHas('photos', fn ($photos) => $photos->count() === 1
                && $photos->first()->original_filename === 'Liburan-Bali.jpg');
    }

    public function test_filter_by_tag(): void
    {
        $tag = Tag::factory()->create(['slug' => 'polban']);
        $tagged = Photo::factory()->create();
        $tagged->tags()->attach($tag);
        Photo::factory()->create();

        Livewire::test(PhotoGallery::class)
            ->set('tag', 'polban')
            ->assertViewHas('photos', fn ($photos) => $photos->count() === 1
                && $photos->first()->id === $tagged->id);
    }

    public function test_sorting_toggles_direction(): void
    {
        Livewire::test(PhotoGallery::class)
            ->assertSet('sortBy', 'taken_at')
            ->assertSet('sortDir', 'desc')
            ->call('setSort', 'taken_at')
            ->assertSet('sortDir', 'asc')
            ->call('setSort', 'original_filename')
            ->assertSet('sortBy', 'original_filename')
            ->assertSet('sortDir', 'desc');
    }

    public function test_delete_removes_photo_and_file(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('photos/x.jpg', 'data');

        $photo = Photo::factory()->create(['disk' => 'public', 'path' => 'photos/x.jpg']);

        Livewire::test(PhotoGallery::class)
            ->call('delete', $photo->id);

        $this->assertDatabaseMissing('photos', ['id' => $photo->id]);
        Storage::disk('public')->assertMissing('photos/x.jpg');
    }

    public function test_open_and_close_photo_detail(): void
    {
        $photo = Photo::factory()->create();

        Livewire::test(PhotoGallery::class)
            ->call('openPhoto', $photo->id)
            ->assertSet('selected', $photo->id)
            ->assertViewHas('selectedPhoto', fn ($p) => $p?->id === $photo->id)
            ->call('closePhoto')
            ->assertSet('selected', null);
    }

    public function test_gallery_route_renders(): void
    {
        $this->get(route('photos.index'))->assertOk()->assertSeeLivewire(PhotoGallery::class);
    }
}
