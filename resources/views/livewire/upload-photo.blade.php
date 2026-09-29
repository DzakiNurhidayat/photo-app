<div class="upload-form">

    @if (count($uploaded) > 0)
        <div class="success-list">
            <p>Berhasil diupload {{ count($uploaded) }} foto:</p>
            <ul>
                @foreach ($uploaded as $name)
                    <li>{{ $name }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (count($uploadErrors) > 0)
        <div class="error-list">
            <p>Gagal:</p>
            <ul>
                @foreach ($uploadErrors as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form wire:submit="save">
        <div class="drop-area">
            <input type="file" wire:model="photos" multiple accept="image/*" id="photo-input">
            <label for="photo-input">
                Klik atau drag foto ke sini
            </label>
        </div>

        @foreach ($errors->get('photos.*') as $messages)
            @foreach ($messages as $message)
                <p class="error">{{ $message }}</p>
            @endforeach
        @endforeach

        @if (count($photos) > 0)
            <p class="file-count">{{ count($photos) }} foto dipilih</p>

            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 0.5rem; margin: 1rem 0;">
                @foreach ($photos as $index => $photo)
                    <div style="position: relative;">
                        <img
                            src="{{ $photo->temporaryUrl() }}"
                            style="width: 100%; height: 100px; object-fit: cover; border-radius: 6px; display: block;"
                        >
                        <button
                            type="button"
                            wire:click="removePhoto({{ $index }})"
                            style="position: absolute; top: 4px; right: 4px; background: rgba(0,0,0,0.6); color: white; border: none; border-radius: 50%; width: 22px; height: 22px; cursor: pointer; font-size: 12px; line-height: 1;"
                        >✕</button>
                        <p style="font-size: 0.65rem; color: #6b7280; margin: 0.2rem 0 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            {{ $photo->getClientOriginalName() }}
                        </p>
                    </div>
                @endforeach
            </div>

            <button type="submit" wire:loading.attr="disabled">
                <span wire:loading wire:target="save">Mengupload...</span>
                <span wire:loading.remove wire:target="save">Upload {{ count($photos) }} Foto</span>
            </button>
        @endif
    </form>
</div>
