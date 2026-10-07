<div>
    {{-- Header tanggal --}}
    <div style="display:flex; align-items:center; gap:12px; margin-bottom:24px; flex-wrap:wrap;">
        <div style="display:flex; align-items:center; gap:4px;">
            <button wire:click="shift(-1)" class="btn btn-ghost" style="padding:8px; border-radius:50%;" title="Hari sebelumnya">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
            </button>
            <button wire:click="shift(1)" class="btn btn-ghost" style="padding:8px; border-radius:50%;" title="Hari berikutnya">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
            </button>
        </div>
        <div>
            <p style="font-size:13px; color:var(--text-muted);">Kenangan pada</p>
            <h1 style="font-size:22px; font-weight:600; color:var(--text);">{{ $ref->locale('id')->translatedFormat('d F') }}</h1>
        </div>
        @if (! $ref->isToday())
            <button wire:click="today" class="btn btn-ghost" style="margin-left:auto;">Hari ini</button>
        @endif
    </div>

    @if ($total === 0)
        <div style="text-align:center; padding:80px 0; color:var(--text-muted);">
            <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1" style="margin:0 auto 16px; display:block; opacity:.4;">
                <path d="M12 8v4l3 3"/><path d="M3.05 11a9 9 0 1 1 .5 4"/><path d="M3 3v5h5"/>
            </svg>
            <p style="font-size:16px;">Belum ada kenangan pada tanggal ini.</p>
            <p style="font-size:13px; margin-top:6px;">Foto dari {{ $ref->locale('id')->translatedFormat('d F') }} di tahun-tahun sebelumnya akan muncul di sini.</p>
        </div>
    @else
        @foreach ($memories as $group)
            <section style="margin-bottom:32px;">
                <div style="display:flex; align-items:baseline; gap:10px; margin-bottom:12px;">
                    <h2 style="font-size:17px; font-weight:600; color:var(--text);">{{ $group->year }}</h2>
                    <span style="font-size:13px; color:var(--text-muted);">
                        {{ $group->yearsAgo }} tahun lalu · {{ $group->photos->count() }} foto
                    </span>
                </div>

                <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(180px, 1fr)); gap:12px;">
                    @foreach ($group->photos as $photo)
                        <a href="{{ route('photos.index', ['photo' => $photo->id]) }}"
                           style="background:var(--surface); border-radius:12px; overflow:hidden; border:1px solid var(--border); text-decoration:none; display:block; transition:box-shadow .15s;"
                           onmouseenter="this.style.boxShadow='0 4px 16px rgba(0,0,0,.1)'"
                           onmouseleave="this.style.boxShadow='none'">
                            <div style="aspect-ratio:1; overflow:hidden; background:#e9edf2;">
                                <img src="{{ $photo->url() }}" alt="{{ $photo->original_filename }}" style="width:100%; height:100%; object-fit:cover; display:block;">
                            </div>
                            <div style="padding:10px 12px;">
                                <p style="font-size:13px; font-weight:500; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; color:var(--text);">
                                    {{ $photo->caption ?: $photo->original_filename }}
                                </p>
                                <p style="font-size:11px; color:var(--text-muted); margin-top:2px;">
                                    {{ $photo->taken_at->locale('id')->translatedFormat('d M Y') }}
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endforeach
    @endif
</div>
