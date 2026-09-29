<div>
    @if ($saved)
        <p style="color: green;">✓ Perubahan disimpan.</p>
    @endif

    <div style="display: flex; gap: 1.5rem; flex-wrap: wrap;">

        {{-- Preview foto --}}
        <div style="flex: 0 0 auto;">
            <img
                src="{{ $photo->url() }}"
                alt="{{ $photo->original_filename }}"
                style="max-width: 300px; max-height: 300px; object-fit: contain; border-radius: 8px; display: block;"
            >
            <p style="font-size: 0.8rem; color: #6b7280; margin-top: 0.5rem;">{{ $photo->original_filename }}</p>
        </div>

        {{-- Form --}}
        <form wire:submit="save" style="flex: 1; min-width: 260px;">

            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 0.25rem;">Tanggal Diambil</label>
                <input
                    type="datetime-local"
                    wire:model="taken_at"
                    style="width: 100%; padding: 0.4rem 0.6rem; border: 1px solid #d1d5db; border-radius: 6px;"
                >
                @if ($photo->taken_at && !$taken_at)
                    <p style="font-size: 0.75rem; color: #6b7280;">EXIF: {{ $photo->taken_at->format('d M Y H:i') }}</p>
                @endif
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 0.25rem;">Caption</label>
                <textarea
                    wire:model="caption"
                    rows="3"
                    style="width: 100%; padding: 0.4rem 0.6rem; border: 1px solid #d1d5db; border-radius: 6px; resize: vertical;"
                    placeholder="Tulis keterangan foto..."
                ></textarea>
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 0.25rem;">Tags</label>

                {{-- Tag list --}}
                <div style="display: flex; flex-wrap: wrap; gap: 0.4rem; margin-bottom: 0.5rem;">
                    @foreach ($tags as $tag)
                        <span style="background: #e0e7ff; color: #3730a3; padding: 0.2rem 0.6rem; border-radius: 999px; font-size: 0.85rem; display: flex; align-items: center; gap: 0.3rem;">
                            #{{ $tag }}
                            <button type="button" wire:click="removeTag('{{ $tag }}')" style="background: none; border: none; cursor: pointer; color: #6366f1; font-size: 0.75rem; padding: 0; line-height: 1;">✕</button>
                        </span>
                    @endforeach
                </div>

                {{-- Tag input --}}
                <div style="display: flex; gap: 0.5rem;">
                    <input
                        type="text"
                        wire:model="tagInput"
                        wire:keydown.enter.prevent="addTag"
                        placeholder="Tambah tag, tekan Enter"
                        style="flex: 1; padding: 0.4rem 0.6rem; border: 1px solid #d1d5db; border-radius: 6px;"
                    >
                    <button type="button" wire:click="addTag" style="padding: 0.4rem 0.8rem; background: #e0e7ff; color: #3730a3; border: none; border-radius: 6px; cursor: pointer;">
                        + Tambah
                    </button>
                </div>
            </div>

            <button type="submit" style="background: #6366f1; color: white; border: none; padding: 0.6rem 1.5rem; border-radius: 8px; cursor: pointer; font-size: 1rem;">
                Simpan
            </button>

        </form>
    </div>
</div>
