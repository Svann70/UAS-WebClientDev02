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
            <div class="group bg-white border border-stone-200/90 rounded-2xl overflow-hidden hover:border-brand-600/40 hover:shadow-md transition flex flex-col justify-between">
                <div>
                    <!-- Product Image Container with Link -->
                    <a href="{{ route('catalog.show', $product->slug) }}" class="block relative w-full aspect-square bg-stone-100 overflow-hidden border-b border-stone-100">
                        @if ($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center text-stone-400 p-4 text-center">
                                <svg class="w-8 h-8 text-stone-300 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span class="text-[11px] font-medium">Foto Produk</span>
                            </div>
                        @endif
                        <span class="absolute bottom-2 left-2 bg-stone-900/80 backdrop-blur-sm text-stone-200 text-[10px] font-semibold px-2 py-0.5 rounded">
                            {{ $product->store->city }}
                        </span>
                    </a>

                    <!-- Card Details -->
                    <div class="p-3.5 sm:p-4">
                        <a href="{{ route('catalog.store', $product->store->slug) }}" class="text-[11px] font-semibold uppercase tracking-wider text-brand-700 hover:underline mb-1 block truncate">
                            {{ $product->store->name }}
                        </a>
                        <a href="{{ route('catalog.show', $product->slug) }}">
                            <h3 class="font-display text-sm font-bold text-stone-900 line-clamp-2 leading-snug group-hover:text-brand-700 transition">
                                {{ $product->name }}
                            </h3>
                        </a>
                    </div>
                </div>

                <!-- Price & Quick Add Button -->
                <div class="p-3.5 sm:p-4 pt-0">
                    <div class="pt-3 border-t border-stone-100 flex items-center justify-between gap-2">
                        <div>
                            <span class="font-display text-sm sm:text-base font-extrabold text-stone-900 block leading-tight">
                                Rp {{ number_format((float) $product->price, 0, ',', '.') }}
                            </span>
                            <span class="text-[10px] text-stone-400 font-medium">Sisa {{ $product->stock }}</span>
                        </div>

                        @auth
                            @if (auth()->user()->isCustomer())
                                <form method="POST" action="{{ route('cart.add', $product) }}">
                                    @csrf
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="bg-brand-700 hover:bg-brand-800 text-white font-bold text-xs px-3 py-1.5 rounded-xl transition shadow-sm active:scale-95 flex items-center gap-1">
                                        <span>+</span>
                                        <span class="hidden sm:inline">Tambah</span>
                                    </button>
                                </form>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="text-brand-700 hover:bg-brand-50 border border-brand-200 font-bold text-xs px-2.5 py-1.5 rounded-xl transition">
                                + Tambah
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-8">{{ $products->links() }}</div>
@endif
@endsection