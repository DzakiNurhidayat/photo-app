<?php

namespace Tests\Feature;

use App\Models\Photo;
use App\Services\PhotoUploadService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PhotoUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_upload_stores_file_and_creates_record(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('foto.jpg', 800, 600);

        $photo = app(PhotoUploadService::class)->upload($file);

        $this->assertInstanceOf(Photo::class, $photo);
        $this->assertDatabaseHas('photos', [
            'id' => $photo->id,
            'original_filename' => 'foto.jpg',
            'disk' => 'public',
        ]);
        Storage::disk('public')->assertExists($photo->path);
    }

    public function test_upload_defaults_taken_at_when_no_exif(): void
    {
        Storage::fake('public');

        $photo = app(PhotoUploadService::class)->upload(UploadedFile::fake()->image('x.jpg'));

        $this->assertNotNull($photo->taken_at);
    }
}
