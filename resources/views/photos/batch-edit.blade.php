@extends('layouts.app')

@section('title', 'Lengkapi Data Foto — PhotoApp')

@section('content')
    <h1 class="page-heading">Lengkapi Data Foto</h1>
    <p style="color: var(--text-muted); margin-top: -12px; margin-bottom: 24px;">Isi informasi untuk setiap foto yang baru diupload.</p>
    @livewire('batch-edit-photos', ['ids' => $ids])
@endsection
