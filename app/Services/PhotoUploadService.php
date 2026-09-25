<?php

namespace App\Services;

use App\Models\Photo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class PhotoUploadService
{
    public function upload(UploadedFile $file): Photo
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('photos', $filename, 'public');

        if (! $path) {
            throw new \RuntimeException('Gagal menyimpan file: ' . $file->getClientOriginalName());
        }

        $exif = $this->readExif($file->getRealPath());

        return Photo::create([
            'filename'          => $filename,
            'original_filename' => $file->getClientOriginalName(),
            'disk'              => 'public',
            'path'              => $path,
            'mime_type'         => $file->getMimeType(),
            'size'              => $file->getSize(),
            'taken_at'          => $exif['taken_at'],
            'camera_make'       => $exif['camera_make'],
            'camera_model'      => $exif['camera_model'],
            'latitude'          => $exif['latitude'],
            'longitude'         => $exif['longitude'],
            'orientation'       => $exif['orientation'],
        ]);
    }

    private function readExif(string $path): array
    {
        $result = [
            'taken_at'     => null,
            'camera_make'  => null,
            'camera_model' => null,
            'latitude'     => null,
            'longitude'    => null,
            'orientation'  => null,
        ];

        if (! function_exists('exif_read_data')) {
            return $result;
        }

        $exif = @exif_read_data($path, null, false);

        if (! $exif) {
            return $result;
        }

        if (! empty($exif['DateTimeOriginal'])) {
            try {
                $result['taken_at'] = \Carbon\Carbon::createFromFormat('Y:m:d H:i:s', $exif['DateTimeOriginal']);
            } catch (\Exception) {}
        }

        $result['camera_make']  = $exif['Make'] ?? null;
        $result['camera_model'] = $exif['Model'] ?? null;
        $result['orientation']  = isset($exif['Orientation']) ? (int) $exif['Orientation'] : null;

        if (! empty($exif['GPSLatitude']) && ! empty($exif['GPSLongitude'])) {
            $result['latitude']  = $this->gpsToDecimal($exif['GPSLatitude'], $exif['GPSLatitudeRef'] ?? 'N');
            $result['longitude'] = $this->gpsToDecimal($exif['GPSLongitude'], $exif['GPSLongitudeRef'] ?? 'E');
        }

        return $result;
    }

    private function gpsToDecimal(array $gps, string $ref): float
    {
        $degrees = $this->fractionToFloat($gps[0]);
        $minutes = $this->fractionToFloat($gps[1]);
        $seconds = $this->fractionToFloat($gps[2]);

        $decimal = $degrees + ($minutes / 60) + ($seconds / 3600);

        return in_array($ref, ['S', 'W']) ? -$decimal : $decimal;
    }

    private function fractionToFloat(string $fraction): float
    {
        if (str_contains($fraction, '/')) {
            [$num, $den] = explode('/', $fraction);
            return $den != 0 ? (float) $num / (float) $den : 0;
        }

        return (float) $fraction;
    }
}
