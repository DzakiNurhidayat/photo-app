<div>

    {{-- Header --}}
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
        <div></div>
        <button wire:click="$set('showNewGroupForm', true)" class="btn btn-primary" style="display:flex; align-items:center; gap:6px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
            Buat Grup
        </button>
    </div>

    {{-- New group form --}}
    @if ($showNewGroupForm)
        <div style="background:var(--accent-light); border:1px solid var(--accent); border-radius:10px; padding:16px 20px; margin-bottom:20px; display:flex; gap:10px; align-items:center;">
            <input
                type="text"
                wire:model="newGroupName"
                wire:keydown.enter="createGroup"
                wire:keydown.escape="$set('showNewGroupForm', false)"
                placeholder="Nama grup..."
                autofocus
                style="flex:1; border:1px solid var(--accent); border-radius:6px; padding:7px 12px; font-size:14px; background:var(--surface); color:var(--text); outline:none;"
            >
            @error('newGroupName') <span style="color:var(--danger); font-size:12px; flex-shrink:0;">{{ $message }}</span> @enderror
            <button wire:click="createGroup" class="btn btn-primary" style="flex-shrink:0;">Simpan</button>
            <button wire:click="$set('showNewGroupForm', false)" class="btn btn-ghost" style="flex-shrink:0;">Batal</button>
        </div>
    @endif

    {{-- Groups section --}}
    @if ($groups->isNotEmpty())
        <div style="margin-bottom:28px;">
            <p style="font-size:12px; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:.5px; margin-bottom:10px;">Grup Tag</p>
            <div style="display:flex; flex-direction:column; gap:6px;">
                @foreach ($groups as $group)
                    <div style="background:var(--surface); border:1px solid var(--border); border-radius:8px; padding:10px 16px; display:flex; align-items:center; gap:12px;" wire:key="group-{{ $group->id }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" style="color:var(--text-muted); flex-shrink:0;"><path d="M3 7a2 2 0 0 1 2-2h3.93a2 2 0 0 1 1.664.89l.812 1.22A2 2 0 0 0 13.07 8H19a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7z"/></svg>

                        @if ($editingGroupId === $group->id)
                            <input
                                type="text"
                                wire:model="editingGroupName"
                                wire:keydown.enter="saveEditGroup"
                                wire:keydown.escape="cancelEditGroup"
                                autofocus
                                style="flex:1; border:1px solid var(--accent); border-radius:6px; padding:5px 10px; font-size:14px; outline:none;"
                            >
                            @error('editingGroupName') <span style="color:var(--danger); font-size:12px;">{{ $message }}</span> @enderror
                            <button wire:click="saveEditGroup" class="btn btn-primary" style="padding:5px 14px; font-size:13px;">Simpan</button>
                            <button wire:click="cancelEditGroup" class="btn btn-ghost" style="padding:5px 14px; font-size:13px;">Batal</button>
                        @else
                            <span style="flex:1; font-weight:500; font-size:14px;">{{ $group->name }}</span>
                            <span style="font-size:12px; color:var(--text-muted);">{{ $group->tags_count }} tag</span>
                            <button wire:click="startEditGroup({{ $group->id }})" class="btn btn-ghost" style="padding:5px 12px; font-size:13px;">Ubah</button>
                            <button
                                wire:click="deleteGroup({{ $group->id }})"
                                wire:confirm="Hapus grup '{{ $group->name }}'? Tag di dalamnya tidak akan terhapus, hanya dipindahkan ke Tanpa Grup."
                                class="btn btn-danger" style="padding:5px 12px; font-size:13px;">
                                Hapus
                            </button>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Tags table --}}
    @if ($tags->isEmpty())
        <div style="text-align:center; padding:60px 0; color:var(--text-muted);">
            <p>Belum ada tag. Tambahkan tag saat mengedit foto.</p>
        </div>
    @else
        <p style="font-size:12px; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:.5px; margin-bottom:10px;">Semua Tag</p>
        <div style="background:var(--surface); border:1px solid var(--border); border-radius:12px; overflow:hidden;">
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr style="background:var(--bg); border-bottom:1px solid var(--border);">
                        <th style="text-align:left; padding:11px 20px; font-size:12px; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:.5px;">Tag</th>
                        <th style="text-align:left; padding:11px 20px; font-size:12px; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:.5px; width:180px;">Grup</th>
                        <th style="text-align:center; padding:11px 20px; font-size:12px; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:.5px; width:80px;">Foto</th>
                        <th style="padding:11px 20px; width:160px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tags as $tag)
                        <tr style="border-bottom:1px solid var(--border);" wire:key="tag-{{ $tag->id }}">
                            <td style="padding:10px 20px;">
                                @if ($editingId === $tag->id)
                                    <input
                                        type="text"
                                        wire:model="editingName"
                                        wire:keydown.enter="saveEdit"
                                        wire:keydown.escape="cancelEdit"
                                        autofocus
                                        style="border:1px solid var(--accent); border-radius:6px; padding:5px 10px; font-size:14px; width:100%; max-width:260px; outline:none;"
                                    >
                                    @error('editingName') <span style="color:var(--danger); font-size:12px;">{{ $message }}</span> @enderror
                                @else
                                    <span style="font-weight:500;">#{{ $tag->name }}</span>
                                @endif
                            </td>
                            <td style="padding:10px 20px;">
                                <select
                                    wire:change="assignGroup({{ $tag->id }}, $event.target.value)"
                                    style="border:1px solid var(--border); border-radius:6px; padding:5px 8px; font-size:13px; background:var(--surface); color:var(--text); cursor:pointer; outline:none; max-width:160px;">
                                    <option value="">Tanpa grup</option>
                                    @foreach ($groups as $group)
                                        <option value="{{ $group->id }}" @selected($tag->tag_group_id === $group->id)>{{ $group->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td style="text-align:center; padding:10px 20px; color:var(--text-muted); font-size:14px;">
                                {{ $tag->photos_count }}
                            </td>
                            <td style="padding:10px 20px;">
                                <div style="display:flex; gap:8px; justify-content:flex-end;">
                                    @if ($editingId === $tag->id)
                                        <button wire:click="saveEdit" class="btn btn-primary" style="padding:5px 14px; font-size:13px;">Simpan</button>
                                        <button wire:click="cancelEdit" class="btn btn-ghost" style="padding:5px 14px; font-size:13px;">Batal</button>
                                    @else
                                        <button wire:click="startEdit({{ $tag->id }})" class="btn btn-ghost" style="padding:5px 14px; font-size:13px;">Ubah nama</button>
                                        <button
                                            wire:click="delete({{ $tag->id }})"
                                            wire:confirm="Hapus tag #{{ $tag->name }}? Tag akan dihapus dari {{ $tag->photos_count }} foto."
                                            class="btn btn-danger"
                                            style="padding:5px 14px; font-size:13px;">
                                            Hapus
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

</div>
