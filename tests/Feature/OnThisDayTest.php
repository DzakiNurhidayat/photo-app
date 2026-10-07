<?php

namespace Tests\Feature;

use App\Livewire\OnThisDay;
use App\Models\Photo;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OnThisDayTest extends TestCase
{
    use RefreshDatabase;

    public function test_shows_photos_from_same_day_previous_years(): void
    {
        Carbon::setTestNow('2026-06-15 10:00');

        $match = Photo::factory()->takenAt('2023-06-15 08:00')->create();
        $otherDay = Photo::factory()->takenAt('2023-06-14 08:00')->create();
        $thisYear = Photo::factory()->takenAt('2026-06-15 08:00')->create();

        $component = Livewire::test(OnThisDay::class);
        $memories = $component->instance()->memories();

        $ids = $memories->flatMap(fn ($g) => $g->photos->pluck('id'))->all();

        $this->assertContains($match->id, $ids);
        $this->assertNotContains($otherDay->id, $ids);
        $this->assertNotContains($thisYear->id, $ids);
    }

    public function test_groups_by_year_with_years_ago(): void
    {
        Carbon::setTestNow('2026-06-15 10:00');
        Photo::factory()->takenAt('2024-06-15 08:00')->create();

        $memories = Livewire::test(OnThisDay::class)->instance()->memories();

        $this->assertSame(2024, $memories->first()->year);
        $this->assertSame(2, $memories->first()->yearsAgo);
    }

    public function test_shift_changes_date(): void
    {
        Carbon::setTestNow('2026-06-15 10:00');

        Livewire::test(OnThisDay::class)
            ->assertSet('date', '2026-06-15')
            ->call('shift', 1)
            ->assertSet('date', '2026-06-16')
            ->call('today')
            ->assertSet('date', '2026-06-15');
    }
}
