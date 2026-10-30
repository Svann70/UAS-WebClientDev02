@extends('layouts.app')

@section('title', 'Edit Produk')

@section('content')
<div class="max-w-2xl mx-auto bg-white border border-gray-200 rounded-lg p-6">
    <h1 class="text-xl font-bold mb-1">Edit Produk</h1>
    <p class="text-sm text-gray-500 mb-4">Slug: <code class="bg-gray-100 px-1 rounded">{{ $product->slug }}</code></p>

    <form method="POST" action="{{ route('merchant.products.update', $product) }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @method('PUT')
        @include('merchant.products._form')
    </form>
</div>
@endsection