<?php

namespace Tests\Feature;

use App\Livewire\PhotoEvents;
use App\Models\Photo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PhotoEventsTest extends TestCase
{
    use RefreshDatabase;

    public function test_groups_photos_into_events(): void
    {
        Photo::factory()->takenAt('2026-01-01 09:00')->create();
        Photo::factory()->takenAt('2026-01-01 14:00')->create();
        Photo::factory()->takenAt('2026-03-10 09:00')->create();

        Livewire::test(PhotoEvents::class)
            ->assertViewHas('events', fn ($events) => $events->count() === 2);
    }

    public function test_counts_undated_photos(): void
    {
        Photo::factory()->create(['taken_at' => null]);

        Livewire::test(PhotoEvents::class)
            ->assertViewHas('undated', 1);
    }

    public function test_events_route_renders(): void
    {
        $this->get(route('photos.events'))->assertOk();
    }
}
