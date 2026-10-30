@extends('layouts.app')

@section('title', 'Katalog Produk UMKM')

@section('content')
<div class="mb-6">
    <h1 class="font-display text-2xl font-extrabold text-stone-900 tracking-tight">Katalog Pangan & Produk Daerah</h1>
    <p class="text-xs text-stone-500 mt-1">Eksplorasi ragam olahan makanan, bumbu dapur, dan kopi khas nusantara.</p>
</div>

<!-- Filters Bar (Taste-Skill Structured, Warm Ground) -->
<form method="GET" action="{{ route('catalog.index') }}" class="bg-white border border-stone-200/90 rounded-2xl p-4 sm:p-5 mb-8 flex flex-wrap gap-3 items-end shadow-sm">
    <div class="flex-1 min-w-[220px]">
        <label class="block text-xs font-bold text-stone-700 mb-1.5">Kata Kunci</label>
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Ketik nama atau deskripsi..."
               class="w-full bg-stone-50 border border-stone-200 rounded-lg px-3.5 py-2 text-sm text-stone-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 font-medium">
    </div>
    <div class="min-w-[160px]">
        <label class="block text-xs font-bold text-stone-700 mb-1.5">Kategori</label>
        <select name="category" class="w-full bg-stone-50 border border-stone-200 rounded-lg px-3 py-2 text-sm text-stone-900 font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <option value="">Semua Kategori</option>
            @foreach ($categories as $category)
                <option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="min-w-[140px]">
        <label class="block text-xs font-bold text-stone-700 mb-1.5">Urutkan</label>
        <select name="sort" class="w-full bg-stone-50 border border-stone-200 rounded-lg px-3 py-2 text-sm text-stone-900 font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <option value="terbaru" @selected(request('sort') === 'terbaru')>Terbaru</option>
            <option value="termurah" @selected(request('sort') === 'termurah')>Harga Terendah</option>
            <option value="termahal" @selected(request('sort') === 'termahal')>Harga Tertinggi</option>
        </select>
    </div>
    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-5 py-2 rounded-lg text-sm transition shadow-sm">
        Terapkan
    </button>
    @if (request()->hasAny(['q', 'category', 'sort']))
        <a href="{{ route('catalog.index') }}" class="text-xs font-semibold text-stone-500 hover:text-stone-800 px-3 py-2">
            Reset
        </a>
    @endif
</form>

<div class="flex items-center justify-between mb-4 text-xs font-medium text-stone-500">
    <span>Ditemukan <strong class="text-stone-800">{{ $products->total() }}</strong> produk</span>
</div>

@if ($products->isEmpty())
    <div class="bg-white border border-stone-200 rounded-2xl py-16 text-center text-stone-400 text-sm shadow-sm">
        <svg class="w-10 h-10 text-stone-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <p class="font-semibold text-stone-600">Tidak ada produk yang cocok</p>
        <p class="text-xs text-stone-400 mt-1">Coba gunakan kata kunci pencarian yang lebih umum atau reset filter.</p>
    </div>
@else
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 sm:gap-6">
        @foreach ($products as $product)
            <a href="{{ route('catalog.show', $product->slug) }}"
               class="group bg-white border border-stone-200/90 rounded-xl overflow-hidden hover:border-stone-300 hover:shadow-md transition flex flex-col">
                <div class="relative w-full aspect-square bg-stone-100 overflow-hidden border-b border-stone-100">
                    @if ($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-stone-400 text-xs">Tanpa foto</div>
                    @endif
                    <span class="absolute bottom-2 left-2 bg-stone-900/80 backdrop-blur-sm text-stone-200 text-[10px] font-semibold px-2 py-0.5 rounded">
                        {{ $product->store->city }}
                    </span>
                </div>
                <div class="p-4 flex-1 flex flex-col justify-between">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-emerald-700 mb-1 truncate">
                            {{ $product->store->name }}
                        </p>
                        <h3 class="font-display text-sm font-bold text-stone-900 line-clamp-2 leading-snug group-hover:text-emerald-700 transition">
                            {{ $product->name }}
                        </h3>
                    </div>
                    <div class="mt-4 pt-3 border-t border-stone-100 flex items-baseline justify-between">
                        <span class="font-display text-base font-extrabold text-stone-900">
                            Rp {{ number_format((float) $product->price, 0, ',', '.') }}
                        </span>
                        <span class="text-[11px] text-stone-400 font-medium">
                            Sisa {{ $product->stock }}
                        </span>
                    </div>
                </div>
            </a>
        @endforeach
    </div>

    <div class="mt-8">{{ $products->links() }}</div>
@endif
@endsection