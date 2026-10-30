@extends('layouts.app')

@section('title', 'Edit Kategori')

@section('content')
<div class="max-w-lg mx-auto bg-white border border-gray-200 rounded-lg p-6">
    <h1 class="text-xl font-bold mb-1">Edit Kategori</h1>
    <p class="text-sm text-gray-500 mb-4">Slug saat ini: <code class="bg-gray-100 px-1 rounded">{{ $category->slug }}</code></p>

    <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="space-y-4">
        @csrf
        @method('PUT')
        @include('admin.categories._form')
    </form>
</div>
@endsection