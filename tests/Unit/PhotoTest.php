<?php

namespace Tests\Unit;

use App\Models\Photo;
use PHPUnit\Framework\TestCase;

class PhotoTest extends TestCase
{
    public function test_camera_label_drops_redundant_make(): void
    {
        $p = new Photo;
        $p->camera_make = 'Canon';
        $p->camera_model = 'Canon EOS 80D';

        $this->assertSame('Canon EOS 80D', $p->cameraLabel());
    }

    public function test_camera_label_combines_make_and_model(): void
    {
        $p = new Photo;
        $p->camera_make = 'NIKON';
        $p->camera_model = 'D750';

        $this->assertSame('NIKON D750', $p->cameraLabel());
    }

    public function test_camera_label_null_when_empty(): void
    {
        $this->assertNull((new Photo)->cameraLabel());
    }

    public function test_has_location(): void
    {
        $p = new Photo;
        $this->assertFalse($p->hasLocation());

        $p->latitude = -6.9;
        $p->longitude = 107.6;
        $this->assertTrue($p->hasLocation());
        $this->assertStringContainsString('107.6', $p->mapUrl());
    }

    public function test_human_size(): void
    {
        $p = new Photo;

        $p->size = 500;
        $this->assertSame('500 B', $p->humanSize());

        $p->size = 1536;
        $this->assertSame('1.5 KB', $p->humanSize());

        $p->size = 2_097_152;
        $this->assertSame('2 MB', $p->humanSize());
    }

    public function test_orientation_label(): void
    {
        $p = new Photo;
        $p->orientation = 6;
        $this->assertSame('Diputar 90° searah jarum jam', $p->orientationLabel());

        $p->orientation = 99;
        $this->assertNull($p->orientationLabel());
    }

    public function test_has_exif(): void
    {
        $p = new Photo;
        $this->assertFalse($p->hasExif());

        $p->orientation = 1;
        $this->assertTrue($p->hasExif());
    }
}
