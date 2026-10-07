<?php

namespace App\Livewire;

use App\Repositories\Contracts\PhotoRepositoryInterface;
use App\Services\EventGrouper;
use Livewire\Attributes\Url;
use Livewire\Component;

class PhotoEvents extends Component
{
    #[Url]
    public int $gap = 12;

    public function render(PhotoRepositoryInterface $repo, EventGrouper $grouper)
    {
        $events = $grouper->group($repo->datedOrdered(), $this->gap);
        $undated = $repo->countUndated();

        return view('livewire.photo-events', compact('events', 'undated'));
    }
}
