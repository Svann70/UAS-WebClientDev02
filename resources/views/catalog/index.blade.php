@extends('layouts.app')

@section('title', 'Katalog Produk')

@section('content')
<h1 class="text-xl font-bold mb-4">Katalog Produk UMKM</h1>

<form method="GET" action="{{ route('catalog.index') }}" class="bg-white border border-gray-200 rounded-lg p-4 mb-5 flex flex-wrap gap-3 items-end">
    <div class="flex-1 min-w-[200px]">
        <label class="block text-xs font-medium text-gray-600 mb-1">Cari</label>
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Nama atau deskripsi produk"
               class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Kategori</label>
        <select name="category" class="border border-gray-300 rounded px-3 py-2 text-sm">
            <option value="">Semua kategori</option>
            @foreach ($categories as $category)
                <option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Urutkan</label>
        <select name="sort" class="border border-gray-300 rounded px-3 py-2 text-sm">
            <option value="terbaru" @selected(request('sort') === 'terbaru')>Terbaru</option>
            <option value="termurah" @selected(request('sort') === 'termurah')>Harga terendah</option>
            <option value="termahal" @selected(request('sort') === 'termahal')>Harga tertinggi</option>
        </select>
    </div>
    <button type="submit" class="bg-emerald-600 text-white px-4 py-2 rounded text-sm hover:bg-emerald-700">
        Terapkan
    </button>
    @if (request()->hasAny(['q', 'category', 'sort']))
        <a href="{{ route('catalog.index') }}" class="text-sm text-gray-500 hover:underline px-2 py-2">Reset</a>
    @endif
</form>

<p class="text-sm text-gray-500 mb-3">{{ $products->total() }} produk ditemukan</p>

@if ($products->isEmpty())
    <div class="bg-white border border-gray-200 rounded-lg py-12 text-center text-gray-400 text-sm">
        Tidak ada produk yang cocok dengan pencarian Anda.
    </div>
@else
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach ($products as $product)
            <a href="{{ route('catalog.show', $product->slug) }}"
               class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition">
                @if ($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                         class="w-full h-36 object-cover">
                @else
                    <div class="w-full h-36 bg-gray-100 flex items-center justify-center text-gray-400 text-xs">Tanpa foto</div>
                @endif
                <div class="p-3">
                    <p class="text-xs text-emerald-700 mb-1">{{ $product->store->name }}</p>
                    <p class="text-sm font-medium line-clamp-2 mb-1">{{ $product->name }}</p>
                    <p class="text-sm font-bold">Rp {{ number_format((float) $product->price, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $product->category->name }} · Stok {{ $product->stock }}</p>
                </div>
            </a>
        @endforeach
    </div>

    <div class="mt-5">{{ $products->links() }}</div>
@endif
@endsection