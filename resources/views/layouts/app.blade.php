<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PhotoApp')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    @livewireStyles
    <style>
        :root {
            --bg:           #f0f4f9;
            --surface:      #ffffff;
            --border:       #dde3ea;
            --text:         #1f2328;
            --text-muted:   #5f6368;
            --accent:       #1a6fdf;
            --accent-light: #e8f0fe;
            --hover:        #e9edf2;
            --danger:       #c5221f;
            --danger-light: #fce8e6;
            --success:      #188038;
            --sidebar-w:    248px;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            height: 100%;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            font-size: 14px;
            color: var(--text);
            background: var(--bg);
        }

        body { display: flex; flex-direction: column; overflow: hidden; }

        /* ── Topbar ─────────────────────────────────────── */
        .topbar {
            height: 60px;
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            padding: 0 20px;
            gap: 16px;
            flex-shrink: 0;
            z-index: 50;
        }

        .topbar-brand {
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            color: var(--text);
            font-size: 18px;
            font-weight: 600;
            min-width: calc(var(--sidebar-w) - 20px);
        }

        .topbar-brand .icon-wrap {
            width: 36px; height: 36px;
            background: var(--accent-light);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
        }

        .topbar-brand .icon-wrap svg { color: var(--accent); }

        .topbar-brand .brand-text b { color: var(--accent); }

        /* ── App shell ──────────────────────────────────── */
        .app-shell {
            display: flex;
            flex: 1;
            overflow: hidden;
        }

        /* ── Sidebar ────────────────────────────────────── */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--surface);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            padding: 12px 0 24px;
            overflow-y: auto;
            flex-shrink: 0;
        }

        .sidebar-upload-wrap { padding: 4px 16px 12px; }

        .btn-upload {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: var(--accent);
            color: #fff;
            text-decoration: none;
            border-radius: 20px;
            padding: 10px 0;
            font-size: 14px;
            font-weight: 500;
            width: 100%;
            box-shadow: 0 1px 3px rgba(0,0,0,.18);
            transition: background .15s;
        }

        .btn-upload:hover { background: #1557b0; }

        .nav-group { padding: 4px 8px; }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 9px 16px;
            border-radius: 0 20px 20px 0;
            color: var(--text);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            margin-right: 8px;
            transition: background .12s;
            cursor: pointer;
        }

        .nav-item svg { width: 20px; height: 20px; flex-shrink: 0; color: var(--text-muted); }
        .nav-item:hover { background: var(--hover); }
        .nav-item.active { background: var(--accent-light); color: var(--accent); font-weight: 600; }
        .nav-item.active svg { color: var(--accent); }

        .sidebar-divider { height: 1px; background: var(--border); margin: 8px 16px; }

        .sidebar-section-label {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .6px;
            text-transform: uppercase;
            color: var(--text-muted);
            padding: 8px 20px 4px;
        }

        .tag-chip {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 6px 12px 6px 24px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 13px;
            border-radius: 0 20px 20px 0;
            margin-right: 8px;
            transition: background .12s;
        }

        .tag-chip:hover { background: var(--hover); color: var(--text); }

        .tag-chip-count {
            font-size: 11px;
            background: var(--hover);
            padding: 1px 7px;
            border-radius: 999px;
            color: var(--text-muted);
        }

        /* ── Main ───────────────────────────────────────── */
        .main-content {
            flex: 1;
            overflow-y: auto;
            padding: 28px 32px;
        }

        .page-heading {
            font-size: 20px;
            font-weight: 500;
            color: var(--text);
            margin-bottom: 20px;
        }

        /* ── Utility ────────────────────────────────────── */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            border: none;
            transition: background .12s, opacity .12s;
        }

        .btn-primary { background: var(--accent); color: #fff; }
        .btn-primary:hover { background: #1557b0; }

        .btn-ghost { background: transparent; color: var(--text-muted); border: 1px solid var(--border); }
        .btn-ghost:hover { background: var(--hover); color: var(--text); }

        .btn-danger { background: var(--danger-light); color: var(--danger); }
        .btn-danger:hover { background: #f6cbc9; }

        .alert { padding: 12px 16px; border-radius: 8px; font-size: 14px; margin-bottom: 16px; }
        .alert-success { background: #e6f4ea; color: var(--success); border: 1px solid #c6e6d0; }
        .alert-danger  { background: var(--danger-light); color: var(--danger); border: 1px solid #f5c6c6; }

        @media (max-width: 768px) {
            .sidebar { display: none; }
            .main-content { padding: 16px; }
        }
    </style>
</head>
<body>

    {{-- Topbar --}}
    <header class="topbar">
        <a href="{{ route('photos.index') }}" class="topbar-brand">
            <span class="icon-wrap">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <rect width="18" height="18" x="3" y="3" rx="2"/>
                    <circle cx="9" cy="9" r="2"/>
                    <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                </svg>
            </span>
            <span class="brand-text">Photo<b>App</b></span>
        </a>
    </header>

    <div class="app-shell">

        {{-- Sidebar --}}
        <nav class="sidebar">
            <div class="sidebar-upload-wrap">
                <a href="{{ route('photos.upload') }}" class="btn-upload">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="17 8 12 3 7 8"/>
                        <line x1="12" y1="3" x2="12" y2="15"/>
                    </svg>
                    Upload Foto
                </a>
            </div>

            <div class="nav-group">
                <a href="{{ route('photos.index') }}" class="nav-item {{ request()->routeIs('photos.index') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <rect width="18" height="18" x="3" y="3" rx="2"/>
                        <circle cx="9" cy="9" r="2"/>
                        <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                    </svg>
                    Semua Foto
                </a>
                <a href="{{ route('tags.index') }}" class="nav-item {{ request()->routeIs('tags.*') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path d="M12 2H2v10l9.29 9.29c.94.94 2.48.94 3.42 0l6.58-6.58c.94-.94.94-2.48 0-3.42L12 2Z"/>
                        <path d="M7 7h.01"/>
                    </svg>
                    Kelola Tag
                </a>
            </div>

            @php
                $sidebarGroups = \App\Models\TagGroup::with(['tags' => fn($q) => $q->withCount('photos')->orderBy('name')])
                    ->orderBy('sort_order')->orderBy('name')->get();
                $ungroupedTags = \App\Models\Tag::withCount('photos')->whereNull('tag_group_id')->orderBy('name')->get();
                $hasTags = $sidebarGroups->isNotEmpty() || $ungroupedTags->isNotEmpty();
            @endphp

            @if ($hasTags)
                <div class="sidebar-divider"></div>
                <p class="sidebar-section-label">Tags</p>

                {{-- Grouped tags --}}
                @foreach ($sidebarGroups as $group)
                    <div x-data="{ open: true }">
                        <button
                            @click="open = !open"
                            style="width:100%; display:flex; align-items:center; gap:8px; padding:6px 12px 6px 20px; background:none; border:none; cursor:pointer; color:var(--text-muted); font-size:12px; font-weight:600; letter-spacing:.4px; text-transform:uppercase; text-align:left; margin-right:8px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                                 :style="open ? 'transform:rotate(90deg)' : ''" style="transition:transform .15s; flex-shrink:0;">
                                <path d="m9 18 6-6-6-6"/>
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" style="flex-shrink:0;"><path d="M3 7a2 2 0 0 1 2-2h3.93a2 2 0 0 1 1.664.89l.812 1.22A2 2 0 0 0 13.07 8H19a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7z"/></svg>
                            {{ $group->name }}
                        </button>
                        <div x-show="open" x-transition>
                            @foreach ($group->tags as $tag)
                                <a href="{{ route('photos.index', ['tag' => $tag->slug]) }}" class="tag-chip" style="padding-left:36px;">
                                    <span>#{{ $tag->name }}</span>
                                    <span class="tag-chip-count">{{ $tag->photos_count }}</span>
                                </a>
                            @endforeach
                            @if ($group->tags->isEmpty())
                                <p style="padding:4px 20px 4px 36px; font-size:12px; color:var(--text-muted); font-style:italic;">Belum ada tag</p>
                            @endif
                        </div>
                    </div>
                @endforeach

                {{-- Ungrouped tags --}}
                @if ($ungroupedTags->isNotEmpty())
                    @if ($sidebarGroups->isNotEmpty())
                        <p style="font-size:11px; font-weight:600; letter-spacing:.4px; text-transform:uppercase; color:var(--text-muted); padding:8px 20px 4px;">Lainnya</p>
                    @endif
                    @foreach ($ungroupedTags as $tag)
                        <a href="{{ route('photos.index', ['tag' => $tag->slug]) }}" class="tag-chip">
                            <span>#{{ $tag->name }}</span>
                            <span class="tag-chip-count">{{ $tag->photos_count }}</span>
                        </a>
                    @endforeach
                @endif
            @endif
        </nav>

        {{-- Content --}}
        <main class="main-content">
            @yield('content')
        </main>

    </div>

    @livewireScripts
</body>
</html>
