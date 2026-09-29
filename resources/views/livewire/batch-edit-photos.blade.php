<div>
    <div style="display: flex; flex-direction: column; gap: 2rem;">
        @foreach ($data as $photoId => $item)
            <div style="border: 1px solid #e5e7eb; border-radius: 12px; padding: 1.5rem; display: flex; gap: 1.5rem; flex-wrap: wrap;">

                {{-- Preview --}}
                <div style="flex: 0 0 auto;">
                    <img
                        src="{{ $item['photo']->url() }}"
                        style="width: 200px; height: 200px; object-fit: cover; border-radius: 8px; display: block;"
                    >
                    <p style="font-size: 0.75rem; color: #6b7280; margin-top: 0.4rem; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                        {{ $item['photo']->original_filename }}
                    </p>
                    @if ($item['saved'])
                        <p style="color: #059669; font-size: 0.85rem; margin-top: 0.25rem;">✓ Tersimpan</p>
                    @endif
                </div>

                {{-- Form --}}
                <div style="flex: 1; min-width: 260px;">

                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; font-weight: 600; margin-bottom: 0.25rem;">Tanggal Diambil</label>
                        <input
                            type="datetime-local"
                            wire:model="data.{{ $photoId }}.taken_at"
                            style="width: 100%; padding: 0.4rem 0.6rem; border: 1px solid #d1d5db; border-radius: 6px; box-sizing: border-box;"
                        >
                    </div>

                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; font-weight: 600; margin-bottom: 0.25rem;">Caption</label>
                        <textarea
                            wire:model="data.{{ $photoId }}.caption"
                            rows="2"
                            placeholder="Tulis keterangan foto..."
                            style="width: 100%; padding: 0.4rem 0.6rem; border: 1px solid #d1d5db; border-radius: 6px; resize: vertical; box-sizing: border-box;"
                        ></textarea>
                    </div>

                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; font-weight: 600; margin-bottom: 0.25rem;">Tags</label>
                        <div style="display: flex; flex-wrap: wrap; gap: 0.4rem; margin-bottom: 0.5rem;">
                            @foreach ($item['tags'] as $tag)
                                <span style="background: #e0e7ff; color: #3730a3; padding: 0.2rem 0.6rem; border-radius: 999px; font-size: 0.85rem; display: flex; align-items: center; gap: 0.3rem;">
                                    #{{ $tag }}
                                    <button type="button" wire:click="removeTag({{ $photoId }}, '{{ $tag }}')" style="background: none; border: none; cursor: pointer; color: #6366f1; font-size: 0.75rem; padding: 0;">✕</button>
                                </span>
                            @endforeach
                        </div>
                        <div style="display: flex; gap: 0.5rem;">
                            <input
                                type="text"
                                wire:model="data.{{ $photoId }}.tagInput"
                                wire:keydown.enter.prevent="addTag({{ $photoId }})"
                                placeholder="Tambah tag, tekan Enter"
                                style="flex: 1; padding: 0.4rem 0.6rem; border: 1px solid #d1d5db; border-radius: 6px;"
                            >
                            <button type="button" wire:click="addTag({{ $photoId }})" style="padding: 0.4rem 0.8rem; background: #e0e7ff; color: #3730a3; border: none; border-radius: 6px; cursor: pointer;">
                                + Tambah
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        @endforeach
    </div>

    <div style="margin-top: 2rem; display: flex; gap: 1rem;">
        <button wire:click="saveAll" style="background: #6366f1; color: white; border: none; padding: 0.7rem 2rem; border-radius: 8px; cursor: pointer; font-size: 1rem;">
            Simpan Semua & ke Galeri
        </button>
        <a href="{{ route('photos.index') }}" style="padding: 0.7rem 1.5rem; color: #6b7280; text-decoration: none; font-size: 1rem;">
            Lewati
        </a>
    </div>
</div>
