@extends('layouts.app')

@section('title', 'Tambah Kategori')

@section('content')
<div class="max-w-lg mx-auto bg-white border border-gray-200 rounded-lg p-6">
    <h1 class="text-xl font-bold mb-4">Tambah Kategori</h1>

    <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-4">
        @csrf
        @include('admin.categories._form')
    </form>
</div>
@endsection