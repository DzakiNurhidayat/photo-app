<?php

namespace App\Services;

use App\Models\Photo;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class EventGrouper
{
    /**
     * Kelompokkan foto menjadi event berdasarkan kedekatan waktu pengambilan.
     * Foto dengan jarak taken_at <= gapHours dianggap satu event.
     *
     * @param  Collection<int, Photo>  $photos  Harus terurut taken_at menaik.
     * @return Collection<int, object{start:Carbon, end:Carbon, photos:Collection}>
     */
    public function group(Collection $photos, int $gapHours = 12): Collection
    {
        $events = collect();
        $current = collect();
        $prev = null;

        foreach ($photos as $photo) {
            if ($photo->taken_at === null) {
                continue;
            }

            if ($prev !== null && $prev->diffInHours($photo->taken_at) > $gapHours) {
                $events->push($this->makeEvent($current));
                $current = collect();
            }

            $current->push($photo);
            $prev = $photo->taken_at;
        }

        if ($current->isNotEmpty()) {
            $events->push($this->makeEvent($current));
        }

        return $events->sortByDesc(fn ($e) => $e->end->timestamp)->values();
    }

    private function makeEvent(Collection $photos): object
    {
        return (object) [
            'start' => $photos->first()->taken_at,
            'end' => $photos->last()->taken_at,
            'photos' => $photos,
        ];
    }
}
