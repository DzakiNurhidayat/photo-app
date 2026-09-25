<?php

namespace App\Livewire;

use App\Services\PhotoUploadService;
use Livewire\Component;
use Livewire\WithFileUploads;

class UploadPhoto extends Component
{
    use WithFileUploads;

    public $photos = [];
    public bool $uploading = false;
    public array $uploaded = [];
    public array $uploadErrors = [];

    protected function rules(): array
    {
        return [
            'photos.*' => 'required|image|max:20480',
        ];
    }

    public function save(PhotoUploadService $service): void
    {
        \Log::info('save() dipanggil', ['jumlah_foto' => count($this->photos)]);

        $this->validate();

        $this->uploading = true;
        $this->uploaded = [];
        $this->uploadErrors = [];

        foreach ($this->photos as $file) {
            try {
                $photo = $service->upload($file);
                $this->uploaded[] = $photo->original_filename;
            } catch (\Throwable $e) {
                $this->uploadErrors[] = $file->getClientOriginalName() . ': ' . $e->getMessage();
            }
        }

        $this->photos = [];
        $this->uploading = false;
    }

    public function render()
    {
        return view('livewire.upload-photo');
    }
}
