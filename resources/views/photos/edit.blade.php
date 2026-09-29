@extends('layouts.app')

@section('title', 'Edit Foto — PhotoApp')

@section('content')
    <h1 class="page-heading">Edit Foto</h1>
    @livewire('edit-photo', ['photo' => $photo])
@endsection
