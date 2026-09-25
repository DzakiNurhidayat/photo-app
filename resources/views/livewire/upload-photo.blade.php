<div class="upload-form">
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
        @endif

        <button type="submit" wire:loading.attr="disabled">
            <span wire:loading wire:target="save">Mengupload...</span>
            <span wire:loading.remove wire:target="save">Upload</span>
        </button>
    </form>

    @if (count($uploaded) > 0)
        <div class="success-list">
            <p>Berhasil diupload:</p>
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
</div>
