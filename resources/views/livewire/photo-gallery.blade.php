<div x-data
     x-init="document.body.style.overflow = $wire.selected ? 'hidden' : '';
             $watch('$wire.selected', v => document.body.style.overflow = v ? 'hidden' : '')">

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
                        <div wire:click="openPhoto({{ $photo->id }})"
                             style="position:absolute; inset:0; background:rgba(0,0,0,0); display:flex; align-items:flex-end; justify-content:flex-end; padding:8px; gap:6px; transition:background .15s; cursor:pointer;"
                             onmouseenter="this.style.background='rgba(0,0,0,.32)'; this.querySelectorAll('a,button').forEach(el=>el.style.opacity='1')"
                             onmouseleave="this.style.background='rgba(0,0,0,0)'; this.querySelectorAll('a,button').forEach(el=>el.style.opacity='0')">
                            <a href="{{ route('photos.edit', $photo) }}"
                               onclick="event.stopPropagation()"
                               style="background:white; color:var(--text); border-radius:50%; width:32px; height:32px; display:flex; align-items:center; justify-content:center; text-decoration:none; opacity:0; transition:opacity .15s; flex-shrink:0;"
                               title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                            </a>
                            <button
                                wire:click.stop="delete({{ $photo->id }})"
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

    {{-- Detail foto --}}
    @if ($selectedPhoto)
        <style>
            .pd-overlay { position:fixed; inset:0; z-index:100; display:flex; background:rgba(0,0,0,.92); }
            .pd-stage { flex:1; position:relative; display:flex; align-items:center; justify-content:center; min-width:0; padding:56px 64px; }
            .pd-stage img { max-width:100%; max-height:100%; object-fit:contain; box-shadow:0 8px 32px rgba(0,0,0,.5); }
            .pd-icon-btn { position:absolute; background:rgba(255,255,255,.12); color:#fff; border:none; border-radius:50%; width:40px; height:40px; display:flex; align-items:center; justify-content:center; cursor:pointer; transition:background .15s; text-decoration:none; }
            .pd-icon-btn:hover { background:rgba(255,255,255,.25); }
            .pd-panel { width:340px; flex-shrink:0; background:var(--surface); overflow-y:auto; padding:20px 22px; }
            .pd-section { padding:14px 0; border-top:1px solid var(--border); }
            .pd-label { font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.04em; color:var(--text-muted); margin-bottom:10px; }
            .pd-row { display:flex; gap:12px; align-items:flex-start; margin-bottom:12px; font-size:13px; color:var(--text); }
            .pd-row svg { flex-shrink:0; color:var(--text-muted); margin-top:1px; }
            .pd-row small { display:block; color:var(--text-muted); font-size:12px; margin-top:2px; }
            @media (max-width: 800px) {
                .pd-overlay { flex-direction:column; overflow-y:auto; }
                .pd-stage { flex:none; height:60vh; padding:56px 16px 16px; }
                .pd-panel { width:auto; overflow:visible; }
            }
        </style>

        <div class="pd-overlay"
             wire:key="detail-{{ $selectedPhoto->id }}"
             x-data
             @keydown.escape.window="$wire.closePhoto()"
             @if ($prevId) @keydown.arrow-left.window="$wire.openPhoto({{ $prevId }})" @endif
             @if ($nextId) @keydown.arrow-right.window="$wire.openPhoto({{ $nextId }})" @endif>

            <div class="pd-stage" wire:click.self="closePhoto">
                <button class="pd-icon-btn" style="top:12px; left:12px;" wire:click="closePhoto" title="Tutup (Esc)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>

                <div style="position:absolute; top:12px; right:12px; display:flex; gap:8px;">
                    <a href="{{ $selectedPhoto->url() }}" target="_blank" class="pd-icon-btn" style="position:static;" title="Buka ukuran asli">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M15 3h6v6M10 14 21 3M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/></svg>
                    </a>
                    <a href="{{ route('photos.edit', $selectedPhoto) }}" class="pd-icon-btn" style="position:static;" title="Edit">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </a>
                    <button class="pd-icon-btn" style="position:static;" title="Hapus"
                            wire:click="delete({{ $selectedPhoto->id }})"
                            wire:confirm="Hapus foto '{{ addslashes($selectedPhoto->original_filename) }}'?">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                    </button>
                </div>

                @if ($prevId)
                    <button class="pd-icon-btn" style="left:12px; top:50%; transform:translateY(-50%);" wire:click="openPhoto({{ $prevId }})" title="Sebelumnya (←)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
                    </button>
                @endif
                @if ($nextId)
                    <button class="pd-icon-btn" style="right:12px; top:50%; transform:translateY(-50%);" wire:click="openPhoto({{ $nextId }})" title="Berikutnya (→)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                    </button>
                @endif

                <img src="{{ $selectedPhoto->url() }}" alt="{{ $selectedPhoto->original_filename }}"
                     x-on:load="$refs.dims.textContent = $el.naturalWidth + ' × ' + $el.naturalHeight + ' px'">
            </div>

            <aside class="pd-panel">
                <h2 style="font-size:16px; font-weight:600; color:var(--text); word-break:break-all; margin-bottom:6px;">
                    {{ $selectedPhoto->original_filename }}
                </h2>
                @if ($selectedPhoto->caption)
                    <p style="font-size:14px; color:var(--text); line-height:1.5; margin-bottom:14px; white-space:pre-line;">{{ $selectedPhoto->caption }}</p>
                @else
                    <p style="font-size:13px; color:var(--text-muted); margin-bottom:14px;">Belum ada caption.</p>
                @endif

                @if ($selectedPhoto->tags->isNotEmpty())
                    <div style="display:flex; flex-wrap:wrap; gap:6px; margin-bottom:14px;">
                        @foreach ($selectedPhoto->tags as $t)
                            <span style="font-size:12px; background:var(--accent-light); color:var(--accent); padding:3px 10px; border-radius:999px; cursor:pointer;"
                                  wire:click="filterByTag('{{ $t->slug }}')">
                                #{{ $t->name }}
                            </span>
                        @endforeach
                    </div>
                @endif

                <div class="pd-section">
                    <p class="pd-label">Detail</p>

                    <div class="pd-row">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                        <div>
                            @if ($selectedPhoto->taken_at)
                                {{ $selectedPhoto->taken_at->locale('id')->translatedFormat('l, d F Y') }}
                                <small>{{ $selectedPhoto->taken_at->format('H:i') }} · tanggal diambil</small>
                            @else
                                <span style="color:var(--text-muted);">Tanggal diambil tidak diketahui</span>
                            @endif
                        </div>
                    </div>

                    <div class="pd-row">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="3" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                        <div>
                            <span x-ref="dims">—</span>
                            <small>{{ $selectedPhoto->humanSize() }} · {{ $selectedPhoto->mime_type }}</small>
                        </div>
                    </div>

                    <div class="pd-row">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12"/></svg>
                        <div>
                            {{ $selectedPhoto->created_at->locale('id')->translatedFormat('d F Y, H:i') }}
                            <small>diunggah</small>
                        </div>
                    </div>
                </div>

                <div class="pd-section">
                    <p class="pd-label">Info EXIF</p>

                    @if ($selectedPhoto->hasExif())
                        @if ($camera = $selectedPhoto->cameraLabel())
                            <div class="pd-row">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/><circle cx="12" cy="13" r="3"/></svg>
                                <div>{{ $camera }}<small>kamera</small></div>
                            </div>
                        @endif

                        @if ($selectedPhoto->hasLocation())
                            <div class="pd-row">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                <div>
                                    {{ number_format($selectedPhoto->latitude, 6) }}, {{ number_format($selectedPhoto->longitude, 6) }}
                                    <small><a href="{{ $selectedPhoto->mapUrl() }}" target="_blank" rel="noopener" style="color:var(--accent);">Lihat di peta ↗</a></small>
                                </div>
                            </div>
                        @endif

                        @if ($orientation = $selectedPhoto->orientationLabel())
                            <div class="pd-row">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 1 1-3-6.7L21 8"/><path d="M21 3v5h-5"/></svg>
                                <div>{{ $orientation }}<small>orientasi</small></div>
                            </div>
                        @endif
                    @else
                        <p style="font-size:13px; color:var(--text-muted); line-height:1.5;">
                            Foto ini tidak memiliki metadata EXIF. Biasanya terjadi pada foto yang dikompres, misalnya dikirim lewat WhatsApp sebagai gambar.
                        </p>
                    @endif
                </div>
            </aside>
        </div>
    @endif

</div>
