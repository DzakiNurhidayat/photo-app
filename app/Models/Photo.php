<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Photo extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename',
        'original_filename',
        'disk',
        'path',
        'mime_type',
        'size',
        'taken_at',
        'camera_make',
        'camera_model',
        'latitude',
        'longitude',
        'orientation',
        'caption',
    ];

    protected $casts = [
        'taken_at' => 'datetime',
        'size' => 'integer',
        'latitude' => 'float',
        'longitude' => 'float',
        'orientation' => 'integer',
    ];

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'photo_tags');
    }

    public function url(): string
    {
        return \Storage::disk($this->disk)->url($this->path);
    }

    public function cameraLabel(): ?string
    {
        $make = trim((string) $this->camera_make);
        $model = trim((string) $this->camera_model);

        // Banyak kamera sudah menyertakan merek di model, mis. "Canon" + "Canon EOS 80D"
        if ($make !== '' && str_starts_with(strtolower($model), strtolower($make))) {
            $make = '';
        }

        $label = trim("$make $model");

        return $label !== '' ? $label : null;
    }

    public function hasLocation(): bool
    {
        return $this->latitude !== null && $this->longitude !== null;
    }

    public function mapUrl(): ?string
    {
        if (! $this->hasLocation()) {
            return null;
        }

        return "https://www.google.com/maps?q={$this->latitude},{$this->longitude}";
    }

    public function humanSize(): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $size = (float) $this->size;
        $i = 0;

        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }

        return round($size, $i === 0 ? 0 : 1).' '.$units[$i];
    }

    public function orientationLabel(): ?string
    {
        return match ($this->orientation) {
            1 => 'Normal',
            2 => 'Dicerminkan horizontal',
            3 => 'Diputar 180°',
            4 => 'Dicerminkan vertikal',
            5 => 'Dicerminkan & diputar 90°',
            6 => 'Diputar 90° searah jarum jam',
            7 => 'Dicerminkan & diputar 270°',
            8 => 'Diputar 90° berlawanan jarum jam',
            default => null,
        };
    }

    public function hasExif(): bool
    {
        return $this->cameraLabel() !== null
            || $this->hasLocation()
            || $this->orientation !== null;
    }
}
