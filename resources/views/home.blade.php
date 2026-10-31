@extends('layouts.app')

@section('title', 'GANDENG')

@section('content')
<!-- Hero Section (GoFood x Clean Architecture) -->
<section class="relative bg-gradient-to-br from-stone-900 via-stone-800 to-emerald-950 text-stone-100 rounded-3xl p-6 sm:p-10 mb-10 border border-stone-800 shadow-md overflow-hidden">
    <div class="absolute -right-10 -bottom-10 w-72 h-72 rounded-full bg-emerald-600/10 blur-3xl pointer-events-none"></div>
    <div class="relative z-10 max-w-2xl">
        <h1 class="font-display text-2xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-white mb-3 leading-[1.18]">
            Jajanan &amp; Pangan Lokal, Langsung dari Dapur UMKM.
        </h1>
        <p class="text-stone-300 text-xs sm:text-sm mb-6 leading-relaxed">
            Pesan sambal kemasan, keripik sanjai, dan kopi specialty asli dari pengrajin daerah. Pesanan langsung masuk ke kas toko mitra.
        </p>

        <!-- Search Bar with GoFood-Like Clean Input -->
        <form method="GET" action="{{ route('catalog.index') }}" class="flex flex-col sm:flex-row gap-2 max-w-xl">
            <div class="relative flex-1">
                <svg class="absolute left-3.5 top-3.5 w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="q" placeholder="Cari keripik balado, kopi arabika, sambal ijo..."
                       class="w-full bg-white text-stone-900 placeholder:text-stone-400 rounded-xl pl-10 pr-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-600 shadow-sm font-medium">
            </div>
            <button type="submit" class="bg-brand-700 hover:bg-brand-800 text-white font-bold px-6 py-3 rounded-xl text-sm transition shadow-sm shrink-0 flex items-center justify-center gap-2">
                <span>Cari Menu</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </button>
        </form>

        <!-- Quick Search Chips -->
        <div class="mt-4 flex flex-wrap items-center gap-2 text-xs">
            <span class="text-stone-400 font-medium">Cari cepat:</span>
            <a href="{{ route('catalog.index', ['q' => 'balado']) }}" class="bg-white/10 hover:bg-white/20 text-stone-200 px-2.5 py-1 rounded-lg transition text-[11px] font-medium">Balado</a>
            <a href="{{ route('catalog.index', ['q' => 'sambal']) }}" class="bg-white/10 hover:bg-white/20 text-stone-200 px-2.5 py-1 rounded-lg transition text-[11px] font-medium">Sambal</a>
            <a href="{{ route('catalog.index', ['q' => 'kopi']) }}" class="bg-white/10 hover:bg-white/20 text-stone-200 px-2.5 py-1 rounded-lg transition text-[11px] font-medium">Kopi Specialty</a>
            <a href="{{ route('catalog.index', ['q' => 'jahe']) }}" class="bg-white/10 hover:bg-white/20 text-stone-200 px-2.5 py-1 rounded-lg transition text-[11px] font-medium">Gula Jahe</a>
        </div>
    </div>
</section>

<!-- Categories Section (UrbanFood-Inspired Solid Rounded Cards) -->
@if ($categories->isNotEmpty())
<section class="mb-12">
    <div class="flex items-baseline justify-between mb-5">
        <div>
            <h2 class="font-display text-xl font-bold text-stone-900 tracking-tight">Kategori Pilihan</h2>
            <p class="text-xs text-stone-500 mt-0.5">Eksplorasi ragam olahan pangan dari toko mitra</p>
        </div>
        <a href="{{ route('catalog.index') }}" class="text-xs font-bold text-brand-700 hover:text-brand-800 transition flex items-center gap-1 group">
            <span>Lihat Semua</span>
            <span class="group-hover:translate-x-0.5 transition-transform">&rarr;</span>
        </a>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        @foreach ($categories as $category)
            @php
                $cardConfig = match($category->slug) {
                    'sambal-nusantara' => [
                        'bg' => 'bg-gradient-to-b from-[#DC2626] to-[#991B1B]',
                        'subtitle' => 'Olahan Sambal & Rempah',
                        'image' => 'images/categories/sambal.png',
                    ],
                    'kopi-minuman' => [
                        'bg' => 'bg-gradient-to-b from-[#854D0E] to-[#451A03]',
                        'subtitle' => 'Kopi Biji & Seduh Herbal',
                        'image' => 'images/categories/kopi.png',
                    ],
                    'kue-camilan' => [
                        'bg' => 'bg-gradient-to-b from-[#D97706] to-[#92400E]',
                        'subtitle' => 'Kue Kering & Manisan',
                        'image' => 'images/categories/kue.png',
                    ],
                    default => [
                        'bg' => 'bg-gradient-to-b from-[#15803D] to-[#14532D]',
                        'subtitle' => 'Kerupuk & Snack Renyah',
                        'image' => 'images/categories/keripik.png',
                    ],
                };
            @endphp
            <a href="{{ route('catalog.index', ['category' => $category->slug]) }}"
               class="relative {{ $cardConfig['bg'] }} rounded-3xl p-5 sm:p-6 min-h-[240px] sm:min-h-[260px] overflow-hidden flex flex-col justify-between group shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300">
                
                <!-- Text Header (Top-Left, Clean White with Badge) -->
                <div class="relative z-10 text-left">
                    <span class="inline-block bg-white/20 backdrop-blur-md text-white text-[11px] font-bold px-2.5 py-0.5 rounded-full mb-2.5 tracking-wide">
                        {{ $category->products_count }} Menu
                    </span>
                    <h3 class="font-display font-extrabold text-white text-lg sm:text-xl leading-tight tracking-tight">
                        {{ $category->name }}
                    </h3>
                    <p class="text-white/80 text-xs font-medium mt-1 leading-snug">
                        {{ $cardConfig['subtitle'] }}
                    </p>
                </div>

                <!-- Bottom Cutout PNG with Multi-layer Ambient Glow & Natural Floor Shadow -->
                <div class="relative z-10 w-full flex flex-col items-center justify-end pt-3">
                    <!-- Soft Radial Ambient Light Behind Product -->
                    <div class="absolute bottom-4 w-32 h-32 rounded-full bg-white/15 blur-2xl pointer-events-none"></div>

                    <!-- Realistic Floor Contact Shadow -->
                    <div class="absolute bottom-0.5 w-24 sm:w-28 h-3.5 bg-black/35 rounded-[50%] blur-md pointer-events-none transform group-hover:scale-95 group-hover:opacity-60 transition-all duration-300"></div>

                    <!-- Cutout Image with Soft Dual-Tone Shadow -->
                    <div class="relative w-36 h-32 sm:w-40 sm:h-36 flex items-center justify-center transform group-hover:scale-108 group-hover:-translate-y-1.5 transition-all duration-300 pointer-events-none">
                        <img src="{{ asset($cardConfig['image']) }}"
                             alt="{{ $category->name }}"
                             class="max-w-full max-h-full object-contain filter drop-shadow-[0_10px_14px_rgba(0,0,0,0.28)] drop-shadow-[0_2px_4px_rgba(0,0,0,0.15)]">
                    </div>
                </div>

                <!-- Corner Ambient Highlight -->
                <div class="absolute -top-10 -right-10 w-36 h-36 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
            </a>
        @endforeach
    </div>
</section>
@endif

<!-- Latest Products Grid -->
<section>
    <div class="flex items-baseline justify-between mb-5">
        <div>
            <h2 class="font-display text-xl font-bold text-stone-900 tracking-tight">Etalase Terbaru</h2>
            <p class="text-xs text-stone-500 mt-0.5">Produk yang baru saja ditambahkan oleh mitra UMKM</p>
        </div>
        <a href="{{ route('catalog.index') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800 transition flex items-center gap-1">
            <span>Buka Katalog Penuh</span>
            <span>&rarr;</span>
        </a>
    </div>

    @if ($products->isEmpty())
        <div class="bg-white border border-stone-200 rounded-xl py-14 text-center text-stone-400 text-sm shadow-sm">
            Belum ada produk aktif yang tersedia di katalog saat ini.
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
    @endif
</section>
@endsection