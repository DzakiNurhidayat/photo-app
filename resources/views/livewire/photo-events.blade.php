<div>
    <div style="display:flex; align-items:center; gap:12px; margin-bottom:24px; flex-wrap:wrap;">
        <div>
            <h1 style="font-size:22px; font-weight:600; color:var(--text);">Event</h1>
            <p style="font-size:13px; color:var(--text-muted);">Foto dikelompokkan otomatis berdasarkan waktu pengambilan.</p>
        </div>
        <div style="display:flex; align-items:center; gap:6px; margin-left:auto;">
            <span style="font-size:13px; color:var(--text-muted);">Jeda antar event:</span>
            @foreach ([6 => '6 jam', 12 => '12 jam', 24 => '1 hari', 72 => '3 hari'] as $h => $label)
                <button wire:click="$set('gap', {{ $h }})"
                        style="padding:6px 12px; border-radius:20px; font-size:13px; font-weight:500; cursor:pointer; border:1px solid var(--border); background:{{ $gap === $h ? 'var(--accent-light)' : 'var(--surface)' }}; color:{{ $gap === $h ? 'var(--accent)' : 'var(--text-muted)' }};">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    @if ($events->isEmpty())
        <div style="text-align:center; padding:80px 0; color:var(--text-muted);">
            <p style="font-size:16px;">Belum ada foto dengan tanggal pengambilan.</p>
        </div>
    @else
        @foreach ($events as $event)
            @php
                $sameDay = $event->start->isSameDay($event->end);
                $rangeLabel = $sameDay
                    ? $event->start->locale('id')->translatedFormat('l, d F Y')
                    : $event->start->locale('id')->translatedFormat('d F') . ' – ' . $event->end->locale('id')->translatedFormat('d F Y');
            @endphp
            <section style="margin-bottom:32px;">
                <div style="display:flex; align-items:baseline; gap:10px; margin-bottom:12px;">
                    <h2 style="font-size:17px; font-weight:600; color:var(--text);">{{ $rangeLabel }}</h2>
                    <span style="font-size:13px; color:var(--text-muted);">{{ $event->photos->count() }} foto</span>
                </div>

                <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(160px, 1fr)); gap:12px;">
                    @foreach ($event->photos as $photo)
                        <a href="{{ route('photos.index', ['photo' => $photo->id]) }}"
                           style="aspect-ratio:1; border-radius:12px; overflow:hidden; background:#e9edf2; display:block; border:1px solid var(--border);">
                            <img src="{{ $photo->url() }}" alt="{{ $photo->original_filename }}" style="width:100%; height:100%; object-fit:cover; display:block;">
                        </a>
                    @endforeach
                </div>
            </section>
        @endforeach
    @endif

    @if ($undated > 0)
        <p style="font-size:13px; color:var(--text-muted); margin-top:8px;">
            {{ $undated }} foto tidak punya tanggal pengambilan dan tidak masuk event.
        </p>
    @endif
</div>
