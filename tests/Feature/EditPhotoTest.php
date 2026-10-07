<?php

namespace Tests\Feature;

use App\Livewire\EditPhoto;
use App\Models\Photo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EditPhotoTest extends TestCase
{
    use RefreshDatabase;

    public function test_saves_caption_and_creates_tags(): void
    {
        $photo = Photo::factory()->create(['caption' => null]);

        Livewire::test(EditPhoto::class, ['photo' => $photo])
            ->set('caption', 'Sidang TA')
            ->set('tagInput', 'polban')
            ->call('addTag')
            ->set('tagInput', '2026')
            ->call('addTag')
            ->call('save')
            ->assertSet('saved', true);

        $photo->refresh();
        $this->assertSame('Sidang TA', $photo->caption);
        $this->assertEqualsCanonicalizing(['polban', '2026'], $photo->tags->pluck('name')->all());
        $this->assertDatabaseHas('tags', ['slug' => 'polban']);
    }

    public function test_duplicate_tag_is_ignored(): void
    {
        $photo = Photo::factory()->create();

        Livewire::test(EditPhoto::class, ['photo' => $photo])
            ->set('tagInput', 'bali')
            ->call('addTag')
            ->set('tagInput', 'bali')
            ->call('addTag')
            ->assertCount('tags', 1);
    }
}
