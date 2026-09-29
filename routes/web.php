<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('photos.gallery');
})->name('photos.index');

Route::get('/upload', function () {
    return view('photos.upload');
})->name('photos.upload');

Route::get('/photos/{photo}/edit', function (\App\Models\Photo $photo) {
    return view('photos.edit', compact('photo'));
})->name('photos.edit');

Route::get('/tags', function () {
    return view('tags.index');
})->name('tags.index');

Route::get('/photos/batch-edit', function () {
    $ids = request('ids');
    return view('photos.batch-edit', compact('ids'));
})->name('photos.batch-edit');
