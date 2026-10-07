<?php

namespace Tests\Unit;

use App\Models\Photo;
use App\Services\EventGrouper;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Tests\TestCase;

class EventGrouperTest extends TestCase
{
    private function photo(string $takenAt): Photo
    {
        $p = new Photo;
        $p->taken_at = Carbon::parse($takenAt);

        return $p;
    }

    public function test_photos_within_gap_are_one_event(): void
    {
        $photos = new Collection([
            $this->photo('2026-01-01 09:00'),
            $this->photo('2026-01-01 11:00'),
            $this->photo('2026-01-01 15:00'),
        ]);

        $events = (new EventGrouper)->group($photos, 12);

        $this->assertCount(1, $events);
        $this->assertCount(3, $events->first()->photos);
    }

    public function test_gap_larger_than_threshold_splits_events(): void
    {
        $photos = new Collection([
            $this->photo('2026-01-01 09:00'),
            $this->photo('2026-01-03 09:00'),
        ]);

        $events = (new EventGrouper)->group($photos, 12);

        $this->assertCount(2, $events);
    }

    public function test_events_sorted_by_most_recent_first(): void
    {
        $photos = new Collection([
            $this->photo('2026-01-01 09:00'),
            $this->photo('2026-06-01 09:00'),
        ]);

        $events = (new EventGrouper)->group($photos, 12);

        $this->assertTrue($events->first()->end->greaterThan($events->last()->end));
    }

    public function test_photos_without_taken_at_are_skipped(): void
    {
        $undated = new Photo;
        $undated->taken_at = null;

        $photos = new Collection([
            $this->photo('2026-01-01 09:00'),
            $undated,
        ]);

        $events = (new EventGrouper)->group($photos, 12);

        $this->assertCount(1, $events);
        $this->assertCount(1, $events->first()->photos);
    }
}
