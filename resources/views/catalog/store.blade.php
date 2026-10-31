@extends('layouts.app')

@section('title', $store->name . ' — Mitra GANDENG')

@section('content')
<!-- GoFood-Style Store Header -->
<div class="bg-white border border-stone-200/90 rounded-2xl p-4 sm:p-8 mb-6 sm:mb-8 shadow-sm">
    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 sm:gap-5">
        @if ($store->logo)
            <img src="{{ asset('storage/' . $store->logo) }}" alt="{{ $store->name }}"
                 class="w-16 h-16 sm:w-24 sm:h-24 rounded-2xl object-cover border border-stone-200 shadow-sm shrink-0">
        @else
            <div class="w-16 h-16 sm:w-24 sm:h-24 rounded-2xl bg-brand-50 border border-brand-200 text-brand-700 flex items-center justify-center font-display font-extrabold text-2xl sm:text-3xl shadow-sm shrink-0">
                {{ mb_substr($store->name, 0, 1) }}
            </div>
        @endif

        <div class="flex-1 min-w-0">
            <div class="flex flex-wrap items-center gap-2 mb-1">
                <span class="inline-flex items-center gap-1 bg-brand-50 text-brand-700 border border-brand-200/60 px-2 py-0.5 rounded-full text-[10px] sm:text-[11px] font-bold">
                    <svg class="w-3 h-3 text-brand-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    Mitra UMKM Juara
                </span>
                <span class="inline-flex items-center gap-1 text-stone-500 text-[11px] sm:text-xs font-medium">
                    <svg class="w-3.5 h-3.5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    {{ $store->city }}
                </span>
            </div>

            <h1 class="font-display text-xl sm:text-3xl font-extrabold text-stone-900 tracking-tight mb-1.5 sm:mb-2">
                {{ $store->name }}
            </h1>

            @if ($store->description)
                <p class="text-xs sm:text-sm text-stone-600 leading-relaxed max-w-2xl">
                    {{ $store->description }}
                </p>
            @endif

            <div class="mt-3 sm:mt-4 pt-2.5 sm:pt-3 border-t border-stone-100 flex items-center gap-3 sm:gap-4 text-[11px] sm:text-xs font-semibold text-stone-500">
                <span>{{ $products->total() }} Menu Tersedia</span>
                <span>&bull;</span>
                <span class="text-amberbrand-600">Pengiriman Daerah</span>
            </div>
        </div>
    </div>
</div>

<!-- Products Section (GoFood-Style Horizontal Menu Cards) -->
<div class="mb-5 flex items-baseline justify-between">
    <div>
        <h2 class="font-display text-xl font-bold text-stone-900 tracking-tight">Daftar Menu & Produk</h2>
        <p class="text-xs text-stone-500 mt-0.5">Pesan langsung dari dapur toko mitra</p>
    </div>
</div>

@if ($products->isEmpty())
    <div class="bg-white border border-stone-200 rounded-2xl py-14 text-center text-stone-400 text-sm shadow-sm">
        Toko ini belum memiliki produk aktif di katalog.
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach ($products as $product)
            <div class="bg-white border border-stone-200/90 rounded-2xl p-4 hover:border-brand-600/40 hover:shadow-sm transition flex gap-4 items-start justify-between">
                <!-- Info Kiri -->
                <div class="flex-1 min-w-0 pr-2">
                    <span class="inline-block px-2 py-0.5 bg-stone-100 text-stone-600 text-[10px] font-bold rounded-md mb-1.5">
                        {{ $product->category->name }}
                    </span>
                    <a href="{{ route('catalog.show', $product->slug) }}" class="block">
                        <h3 class="font-display font-bold text-stone-900 text-sm sm:text-base hover:text-brand-700 transition leading-snug line-clamp-1">
                            {{ $product->name }}
                        </h3>
                    </a>
                    @if ($product->description)
                        <p class="text-xs text-stone-500 line-clamp-2 mt-1 leading-relaxed">
                            {{ $product->description }}
                        </p>
                    @endif
                    
                    <div class="mt-3 flex items-baseline gap-3">
                        <span class="font-display font-extrabold text-stone-900 text-sm sm:text-base">
                            Rp {{ number_format((float) $product->price, 0, ',', '.') }}
                        </span>
                        <span class="text-[11px] text-stone-400 font-medium">
                            Sisa {{ $product->stock }}
                        </span>
                    </div>
                </div>

                <!-- Foto Kanan + Tombol Tambah -->
                <div class="flex flex-col items-end shrink-0 gap-2">
                    <a href="{{ route('catalog.show', $product->slug) }}" class="w-24 h-24 sm:w-28 sm:h-28 rounded-xl overflow-hidden bg-stone-100 block border border-stone-100">
                        @if ($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover hover:scale-105 transition duration-200">
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center text-stone-400 text-xs">
                                <svg class="w-6 h-6 text-stone-300 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span>Menu</span>
                            </div>
                        @endif
                    </a>

                    @auth
                        @if (auth()->user()->isCustomer())
                            <form method="POST" action="{{ route('cart.add', $product) }}">
                                @csrf
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="bg-brand-700 hover:bg-brand-800 text-white font-bold text-xs px-4 py-1.5 rounded-full shadow-sm transition active:scale-95 flex items-center gap-1">
                                    <span>+</span> Tambah
                                </button>
                            </form>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="text-brand-700 hover:bg-brand-50 border border-brand-200 font-bold text-xs px-3.5 py-1.5 rounded-full transition">
                            + Tambah
                        </a>
                    @endauth
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-8">
        {{ $products->links() }}
    </div>
@endif
@endsection