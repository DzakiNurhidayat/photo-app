<?php

namespace Database\Factories;

use App\Models\Photo;
use Illuminate\Database\Eloquent\Factories\Factory;

class PhotoFactory extends Factory
{
    protected $model = Photo::class;

    public function definition(): array
    {
        $name = $this->faker->uuid();

        return [
            'filename' => $name.'.jpg',
            'original_filename' => $this->faker->words(2, true).'.jpg',
            'disk' => 'public',
            'path' => 'photos/'.$name.'.jpg',
            'mime_type' => 'image/jpeg',
            'size' => $this->faker->numberBetween(100_000, 5_000_000),
            'taken_at' => $this->faker->dateTimeBetween('-5 years', 'now'),
            'camera_make' => null,
            'camera_model' => null,
            'latitude' => null,
            'longitude' => null,
            'orientation' => null,
            'caption' => null,
        ];
    }

    public function takenAt(string|\DateTimeInterface $when): static
    {
        return $this->state(fn () => ['taken_at' => $when]);
    }

    public function withExif(): static
    {
        return $this->state(fn () => [
            'camera_make' => 'Canon',
            'camera_model' => 'Canon EOS 80D',
            'latitude' => -6.914744,
            'longitude' => 107.609810,
            'orientation' => 1,
        ]);
    }
}
