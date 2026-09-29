@extends('layouts.app')

@section('title', 'Semua Foto — PhotoApp')

@section('content')
    <h1 class="page-heading">Semua Foto</h1>
    @livewire('photo-gallery')
@endsection
