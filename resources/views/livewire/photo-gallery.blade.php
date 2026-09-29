<div>

    {{-- Filter bar --}}
    <div style="display:flex; align-items:center; gap:10px; margin-bottom:20px; flex-wrap:wrap;">

        {{-- Search --}}
        <div style="position:relative; flex:1; min-width:200px; max-width:360px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                 style="position:absolute; left:10px; top:50%; transform:translateY(-50%); color:var(--text-muted); pointer-events:none;">
                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
            </svg>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Cari nama file atau caption..."
                style="width:100%; padding:8px 12px 8px 34px; border:1px solid var(--border); border-radius:8px; font-size:14px; background:var(--surface); color:var(--text); outline:none;"
                onfocus="this.style.borderColor='var(--accent)'"
                onblur="this.style.borderColor='var(--border)'"
            >
        </div>

        {{-- Sort --}}
        <div style="display:flex; align-items:center; gap:6px; flex-shrink:0;">
            <span style="font-size:13px; color:var(--text-muted);">Urutkan:</span>

            @php
                $sorts = [
                    'taken_at'          => 'Tanggal foto',
                    'created_at'        => 'Tanggal upload',
                    'original_filename' => 'Nama file',
                ];
            @endphp

            @foreach ($sorts as $key => $label)
                <button
                    wire:click="setSort('{{ $key }}')"
                    style="padding:6px 12px; border-radius:20px; font-size:13px; font-weight:500; cursor:pointer; border:1px solid var(--border); background:{{ $sortBy === $key ? 'var(--accent-light)' : 'var(--surface)' }}; color:{{ $sortBy === $key ? 'var(--accent)' : 'var(--text-muted)' }}; display:flex; align-items:center; gap:4px; transition:background .1s;">
                    {{ $label }}
                    @if ($sortBy === $key)
                        @if ($sortDir === 'desc')
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12l7 7 7-7"/></svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M12 19V5M5 12l7-7 7 7"/></svg>
                        @endif
                    @endif
                </button>
            @endforeach
        </div>
    </div>

    {{-- Active tag filter chip --}}
    @if ($activeTag)
        <div style="display:flex; align-items:center; gap:8px; margin-bottom:16px;">
            <span style="font-size:13px; color:var(--text-muted);">Filter aktif:</span>
            <span style="background:var(--accent-light); color:var(--accent); padding:4px 12px; border-radius:999px; font-size:13px; font-weight:500; display:flex; align-items:center; gap:6px;">
                #{{ $activeTag->name }}
                <button wire:click="clearTag" style="background:none; border:none; cursor:pointer; color:var(--accent); font-size:14px; line-height:1; padding:0;">✕</button>
            </span>
        </div>
    @endif

    {{-- Empty state --}}
    @if ($photos->isEmpty())
        <div style="text-align:center; padding:80px 0; color:var(--text-muted);">
            <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1" style="margin:0 auto 16px; display:block; opacity:.4;">
                <rect width="18" height="18" x="3" y="3" rx="2"/>
                <circle cx="9" cy="9" r="2"/>
                <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
            </svg>
            @if ($search || $tag)
                <p style="font-size:16px;">Tidak ada foto yang cocok.</p>
            @else
                <p style="font-size:16px; margin-bottom:12px;">Belum ada foto</p>
                <a href="{{ route('photos.upload') }}" class="btn btn-primary" style="display:inline-flex;">Upload Foto Pertama</a>
            @endif
        </div>
    @else
        {{-- Count --}}
        <p style="font-size:13px; color:var(--text-muted); margin-bottom:12px;">{{ $photos->count() }} foto</p>

        {{-- Grid --}}
        <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(180px, 1fr)); gap:12px;">
            @foreach ($photos as $photo)
                <div style="background:var(--surface); border-radius:12px; overflow:hidden; border:1px solid var(--border); position:relative; transition:box-shadow .15s;"
                     onmouseenter="this.style.boxShadow='0 4px 16px rgba(0,0,0,.1)'"
                     onmouseleave="this.style.boxShadow='none'">

                    <div style="position:relative; aspect-ratio:1; overflow:hidden; background:#e9edf2;">
                        <img
                            src="{{ $photo->url() }}"
                            alt="{{ $photo->original_filename }}"
                            style="width:100%; height:100%; object-fit:cover; display:block;"
                        >
                        {{-- Hover overlay --}}
                        <div style="position:absolute; inset:0; background:rgba(0,0,0,0); display:flex; align-items:flex-end; justify-content:flex-end; padding:8px; gap:6px; transition:background .15s;"
                             onmouseenter="this.style.background='rgba(0,0,0,.32)'; this.querySelectorAll('a,button').forEach(el=>el.style.opacity='1')"
                             onmouseleave="this.style.background='rgba(0,0,0,0)'; this.querySelectorAll('a,button').forEach(el=>el.style.opacity='0')">
                            <a href="{{ route('photos.edit', $photo) }}"
                               style="background:white; color:var(--text); border-radius:50%; width:32px; height:32px; display:flex; align-items:center; justify-content:center; text-decoration:none; opacity:0; transition:opacity .15s; flex-shrink:0;"
                               title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                            </a>
                            <button
                                wire:click="delete({{ $photo->id }})"
                                wire:confirm="Hapus foto '{{ addslashes($photo->original_filename) }}'?"
                                style="background:white; color:var(--danger); border:none; border-radius:50%; width:32px; height:32px; display:flex; align-items:center; justify-content:center; cursor:pointer; opacity:0; transition:opacity .15s; flex-shrink:0;"
                                title="Hapus">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <polyline points="3 6 5 6 21 6"/>
                                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                    <path d="M10 11v6M14 11v6"/>
                                    <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div style="padding:10px 12px;">
                        <p style="font-size:13px; font-weight:500; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; color:var(--text);">
                            {{ $photo->original_filename }}
                        </p>
                        <p style="font-size:11px; color:var(--text-muted); margin-top:2px;">
                            {{ $photo->taken_at?->format('d M Y') ?? '—' }}
                        </p>
                        @if ($photo->tags->isNotEmpty())
                            <div style="display:flex; flex-wrap:wrap; gap:3px; margin-top:6px;">
                                @foreach ($photo->tags->take(3) as $tag)
                                    <span style="font-size:10px; background:var(--accent-light); color:var(--accent); padding:2px 8px; border-radius:999px; cursor:pointer;"
                                          wire:click="$set('tag', '{{ $tag->slug }}')">
                                        #{{ $tag->name }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
