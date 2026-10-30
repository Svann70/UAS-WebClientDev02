@extends('layouts.app')

@section('title', $store->name)

@section('content')
<div class="bg-white border border-gray-200 rounded-lg p-6 mb-6 flex items-start gap-5">
    @if ($store->logo)
        <img src="{{ asset('storage/' . $store->logo) }}" alt="{{ $store->name }}"
             class="w-20 h-20 rounded-lg object-cover border border-gray-200">
    @else
        <div class="w-20 h-20 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold text-xl">
            {{ mb_substr($store->name, 0, 1) }}
        </div>
    @endif
    <div>
        <h1 class="text-xl font-bold mb-1">{{ $store->name }}</h1>
        <p class="text-sm text-gray-500 mb-2">{{ $store->city }}</p>
        @if ($store->description)
            <p class="text-sm text-gray-700">{{ $store->description }}</p>
        @endif
        <p class="text-xs text-gray-400 mt-2">{{ $products->total() }} produk tersedia</p>
    </div>
</div>

@if ($products->isEmpty())
    <div class="bg-white border border-gray-200 rounded-lg py-12 text-center text-gray-400 text-sm">
        Toko ini belum menampilkan produk.
    </div>
@else
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach ($products as $product)
            <a href="{{ route('catalog.show', $product->slug) }}"
               class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition">
                @if ($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-36 object-cover">
                @else
                    <div class="w-full h-36 bg-gray-100 flex items-center justify-center text-gray-400 text-xs">Tanpa foto</div>
                @endif
                <div class="p-3">
                    <p class="text-sm font-medium line-clamp-2 mb-1">{{ $product->name }}</p>
                    <p class="text-sm font-bold">Rp {{ number_format((float) $product->price, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $product->category->name }}</p>
                </div>
            </a>
        @endforeach
    </div>
    <div class="mt-5">{{ $products->links() }}</div>
@endif
@endsection