<?php

namespace App\Livewire;

use App\Models\Photo;
use App\Repositories\Contracts\PhotoRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Livewire\Component;

class OnThisDay extends Component
{
    public string $date = '';

    public function mount(): void
    {
        if ($this->date === '') {
            $this->date = Carbon::today()->toDateString();
        }
    }

    public function shift(int $days): void
    {
        $this->date = Carbon::parse($this->date)->addDays($days)->toDateString();
    }

    public function today(): void
    {
        $this->date = Carbon::today()->toDateString();
    }

    /**
     * @return Collection<int, object{year:int, yearsAgo:int, photos:Collection}>
     */
    public function memories(): Collection
    {
        $ref = Carbon::parse($this->date);

        return app(PhotoRepositoryInterface::class)
            ->memoriesFor($ref)
            ->groupBy(fn (Photo $p) => $p->taken_at->year)
            ->map(fn (Collection $photos, $year) => (object) [
                'year' => (int) $year,
                'yearsAgo' => $ref->year - (int) $year,
                'photos' => $photos,
            ])
            ->sortByDesc('year')
            ->values();
    }

    public function render()
    {
        $memories = $this->memories();
        $ref = Carbon::parse($this->date);

        return view('livewire.on-this-day', [
            'memories' => $memories,
            'ref' => $ref,
            'total' => $memories->sum(fn ($g) => $g->photos->count()),
        ]);
    }
}
