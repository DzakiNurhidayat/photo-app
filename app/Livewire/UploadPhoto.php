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

    public function removePhoto(int $index): void
    {
        array_splice($this->photos, $index, 1);
    }

    public function save(PhotoUploadService $service): void
    {
        $this->validate();

        $this->uploading = true;
        $this->uploaded = [];
        $this->uploadErrors = [];

        $uploadedIds = [];

        foreach ($this->photos as $file) {
            try {
                $photo = $service->upload($file);
                $uploadedIds[] = $photo->id;
            } catch (\Throwable $e) {
                $this->uploadErrors[] = $file->getClientOriginalName() . ': ' . $e->getMessage();
            }
        }

        $this->photos = [];
        $this->uploading = false;

        if (! empty($uploadedIds)) {
            $this->redirect(route('photos.batch-edit', ['ids' => implode(',', $uploadedIds)]));
        }
    }

    public function render()
    {
        return view('livewire.upload-photo');
    }
}
