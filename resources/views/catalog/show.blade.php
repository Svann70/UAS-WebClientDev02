@extends('layouts.app')

@section('title', $product->name . ' — ' . $product->store->name)

@section('content')
<!-- Breadcrumbs -->
<nav class="text-xs font-medium text-stone-500 mb-6 flex items-center gap-2">
    <a href="{{ route('home') }}" class="hover:text-stone-900 transition">Beranda</a>
    <span class="text-stone-300">/</span>
    <a href="{{ route('catalog.index') }}" class="hover:text-stone-900 transition">Katalog</a>
    <span class="text-stone-300">/</span>
    <a href="{{ route('catalog.store', $product->store->slug) }}" class="hover:text-stone-900 transition">{{ $product->store->name }}</a>
    <span class="text-stone-300">/</span>
    <span class="text-stone-800 font-semibold truncate max-w-xs">{{ $product->name }}</span>
</nav>

<!-- Main Product Details -->
<div class="bg-white border border-stone-200/90 rounded-2xl p-6 sm:p-8 grid md:grid-cols-2 gap-8 lg:gap-12 shadow-sm">
    <!-- Image Preview Column -->
    <div>
        <div class="w-full aspect-square rounded-xl bg-stone-100 overflow-hidden border border-stone-200/80 flex items-center justify-center">
            @if ($product->image)
                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                     class="w-full h-full object-cover">
            @else
                <div class="flex flex-col items-center justify-center text-stone-400">
                    <svg class="w-12 h-12 text-stone-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span class="text-xs font-medium">Foto produk belum diunggah</span>
                </div>
            @endif
        </div>
    </div>

    <!-- Product Info Column -->
    <div class="flex flex-col justify-between">
        <div>
            <div>
                <span class="inline-block bg-brand-50 text-brand-800 border border-brand-200/80 text-[11px] font-bold px-3 py-1 rounded-full mb-3 uppercase tracking-wider">
                    {{ $product->category->name }}
                </span>

                <h1 class="font-display text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight mb-3 leading-snug">
                    {{ $product->name }}
                </h1>

                <div class="font-display text-3xl font-black text-stone-900 mb-6 pb-6 border-b border-stone-100">
                    Rp {{ number_format((float) $product->price, 0, ',', '.') }}
                </div>

                <!-- Merchant Store Badge -->
                <div class="bg-stone-50 border border-stone-200/70 rounded-2xl p-4 mb-6 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        @if ($product->store->logo)
                            <img src="{{ asset('storage/' . $product->store->logo) }}" class="w-12 h-12 rounded-xl object-cover border border-stone-200 shadow-sm" alt="{{ $product->store->name }}">
                        @else
                            <div class="w-12 h-12 rounded-xl bg-brand-100 text-brand-800 font-bold flex items-center justify-center text-lg">
                                {{ mb_substr($product->store->name, 0, 1) }}
                            </div>
                        @endif
                        <div>
                            <span class="text-[10px] text-stone-500 font-medium block">Disediakan oleh:</span>
                            <a href="{{ route('catalog.store', $product->store->slug) }}" class="font-display font-bold text-stone-900 hover:text-brand-700 transition">
                                {{ $product->store->name }}
                            </a>
                            <span class="text-xs text-stone-500 block mt-0.5">{{ $product->store->city }}</span>
                        </div>
                    </div>
                    <a href="{{ route('catalog.store', $product->store->slug) }}" class="text-xs font-semibold text-brand-700 hover:underline">
                        Kunjungi Toko &rarr;
                    </a>
                </div>

                <div class="space-y-1.5 text-xs text-stone-600 mb-6">
                    <div class="flex items-center gap-2">
                        <span class="font-semibold text-stone-700 w-24">Ketersediaan:</span>
                        @if ($product->stock > 0)
                            <span class="text-brand-700 font-bold bg-brand-50 border border-brand-200/60 px-2.5 py-0.5 rounded-full">Tersedia {{ $product->stock }} unit</span>
                        @else
                            <span class="text-rose-700 font-bold bg-rose-50 px-2 py-0.5 rounded-full">Stok Habis</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="font-semibold text-stone-700 w-24">Pengiriman:</span>
                        <span>Dikirim dari kota {{ $product->store->city }}</span>
                    </div>
                </div>
            </div>

            <!-- Action Area -->
            <div class="pt-4 border-t border-stone-100">
                @auth
                    @if (auth()->user()->isCustomer())
                        @if ($product->stock > 0)
                            <form method="POST" action="{{ route('cart.add', $product->id) }}" class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-end">
                                @csrf
                                <div>
                                    <label class="block text-xs font-bold text-stone-700 mb-1.5">Jumlah Beli</label>
                                    <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}"
                                           class="w-full sm:w-28 border border-stone-300 rounded-xl px-3 py-2.5 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-brand-600">
                                </div>
                                <button type="submit"
                                        class="flex-1 bg-brand-700 hover:bg-brand-800 text-white font-bold px-6 py-3 rounded-xl text-sm transition shadow-sm flex items-center justify-center gap-2 active:scale-95">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                    <span>Tambah ke Keranjang</span>
                                </button>
                            </form>
                        @else
                            <div class="bg-stone-100 text-stone-600 px-4 py-3 rounded-xl text-xs font-medium text-center">
                                Produk ini sedang habis stok. Silakan cek kembali dalam waktu dekat.
                            </div>
                        @endif
                    @else
                        <div class="text-xs text-stone-500 bg-stone-50 border border-stone-200 p-3 rounded-xl">
                            Anda masuk sebagai {{ auth()->user()->role }}. Masuk sebagai akun pembeli untuk memesan produk ini.
                        </div>
                    @endif
                @else
                    <a href="{{ route('login') }}"
                       class="w-full bg-brand-700 hover:bg-brand-800 text-white font-bold px-6 py-3.5 rounded-xl text-sm transition shadow-sm text-center block">
                        Masuk Akun untuk Membeli
                    </a>
                @endauth
            </div>
    </div>
</div>

<!-- Description Section -->
<section class="mt-8 bg-white border border-stone-200/90 rounded-2xl p-6 sm:p-8 shadow-sm">
    <h2 class="font-display text-lg font-bold text-stone-900 mb-4 pb-3 border-b border-stone-100">
        Informasi Lengkap Produk
    </h2>
    <div class="text-sm text-stone-700 leading-relaxed max-w-3xl">
        {!! nl2br(e($product->description)) !!}
    </div>
</section>

<!-- Cross-Sell Section -->
@if ($lainnya->isNotEmpty())
<section class="mt-12">
    <h2 class="font-display text-lg font-bold text-stone-900 mb-4">
        Produk Lain dari {{ $product->store->name }}
    </h2>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        @foreach ($lainnya as $item)
            <a href="{{ route('catalog.show', $item->slug) }}"
               class="group bg-white border border-stone-200/90 rounded-xl overflow-hidden hover:shadow-md transition">
                <div class="w-full h-36 bg-stone-100 overflow-hidden">
                    @if ($item->image)
                        <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-stone-400 text-xs">Tanpa foto</div>
                    @endif
                </div>
                <div class="p-3">
                    <h3 class="font-display text-xs font-bold text-stone-900 line-clamp-1 group-hover:text-emerald-700 transition">
                        {{ $item->name }}
                    </h3>
                    <p class="font-display text-sm font-extrabold text-stone-900 mt-1">
                        Rp {{ number_format((float) $item->price, 0, ',', '.') }}
                    </p>
                </div>
            </a>
        @endforeach
    </div>
</section>
@endif
@endsection