@extends('layouts.app')

@section('title', 'Katalog — GANDENG')

@section('content')
<!-- Hero Section (Matching Home, with Active Search Binding) -->
<section class="relative bg-gradient-to-br from-stone-900 via-stone-800 to-emerald-950 text-stone-100 rounded-2xl sm:rounded-3xl p-5 sm:p-10 mb-8 sm:mb-10 border border-stone-800 shadow-md overflow-hidden">
    <div class="absolute -right-10 -bottom-10 w-72 h-72 rounded-full bg-emerald-600/10 blur-3xl pointer-events-none"></div>
    <div class="relative z-10 max-w-2xl">
        <h1 class="font-display text-2xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-white mb-2 sm:mb-3 leading-snug sm:leading-[1.18]">
            Jajanan &amp; Pangan Lokal, Langsung dari Dapur UMKM.
        </h1>
        <p class="text-stone-300 text-xs sm:text-sm mb-5 sm:mb-6 leading-relaxed">
            Pesan sambal kemasan, keripik sanjai, dan kopi specialty asli dari pengrajin daerah. Pesanan langsung masuk ke kas toko mitra.
        </p>

        <!-- Search Bar with Clean Input -->
        <form method="GET" action="{{ route('catalog.index') }}" class="flex flex-col sm:flex-row gap-2 max-w-xl">
            @if(request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
            @if(request('sort'))
                <input type="hidden" name="sort" value="{{ request('sort') }}">
            @endif
            <div class="relative flex-1">
                <svg class="absolute left-3.5 top-3 sm:top-3.5 w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari keripik balado, kopi arabika, sambal..."
                       class="w-full bg-white text-stone-900 placeholder:text-stone-400 rounded-xl pl-10 pr-4 py-2.5 sm:py-3 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-brand-600 shadow-sm font-medium">
            </div>
            <button type="submit" class="bg-brand-700 hover:bg-brand-800 text-white font-bold px-5 py-2.5 sm:px-6 sm:py-3 rounded-xl text-xs sm:text-sm transition shadow-sm shrink-0 flex items-center justify-center gap-2">
                <span>Cari Menu</span>
                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </button>
        </form>

        <!-- Quick Search Chips -->
        <div class="mt-3.5 sm:mt-4 flex flex-wrap items-center gap-1.5 sm:gap-2 text-[11px] sm:text-xs">
            <span class="text-stone-400 font-medium">Cari cepat:</span>
            <a href="{{ route('catalog.index', ['q' => 'balado']) }}" class="bg-white/10 hover:bg-white/20 text-stone-200 px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-lg transition font-medium">Balado</a>
            <a href="{{ route('catalog.index', ['q' => 'sambal']) }}" class="bg-white/10 hover:bg-white/20 text-stone-200 px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-lg transition font-medium">Sambal</a>
            <a href="{{ route('catalog.index', ['q' => 'kopi']) }}" class="bg-white/10 hover:bg-white/20 text-stone-200 px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-lg transition font-medium">Kopi</a>
            <a href="{{ route('catalog.index', ['q' => 'jahe']) }}" class="bg-white/10 hover:bg-white/20 text-stone-200 px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-lg transition font-medium">Gula Jahe</a>
        </div>
    </div>
</section>

<!-- Categories Section (Mobile Optimized UrbanFood Grid) -->
@if ($categories->isNotEmpty())
<section class="mb-10 sm:mb-12">
    <div class="flex items-baseline justify-between mb-4 sm:mb-5">
        <div>
            <h2 class="font-display text-lg sm:text-xl font-bold text-stone-900 tracking-tight">Kategori Pilihan</h2>
            <p class="text-[11px] sm:text-xs text-stone-500 mt-0.5">Eksplorasi ragam olahan pangan dari toko mitra</p>
        </div>
        @if(request('category'))
            <a href="{{ route('catalog.index', array_filter(['q' => request('q'), 'sort' => request('sort')])) }}" class="text-xs font-bold text-brand-700 hover:text-brand-800 transition">
                Tampilkan Semua Kategori &rarr;
            </a>
        @endif
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6">
        @foreach ($categories as $category)
            @php
                $isActive = request('category') === $category->slug;
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
            <a href="{{ route('catalog.index', array_filter(['category' => $category->slug, 'q' => request('q'), 'sort' => request('sort')])) }}"
               class="relative {{ $cardConfig['bg'] }} rounded-2xl sm:rounded-3xl p-3.5 sm:p-6 h-[210px] sm:h-[260px] overflow-hidden flex flex-col justify-between group shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 {{ $isActive ? 'ring-4 ring-brand-600 ring-offset-2' : '' }}">
                
                <!-- Text Header (Top-Left, Clean White with Badge) -->
                <div class="relative z-10 text-left">
                    <span class="inline-block {{ $isActive ? 'bg-white text-stone-900' : 'bg-white/20 text-white' }} backdrop-blur-md text-[10px] sm:text-[11px] font-bold px-2 py-0.5 rounded-full mb-1.5 sm:mb-2.5 tracking-wide transition">
                        {{ $category->products_count ?? $category->products()->count() }} Menu {{ $isActive ? '• Terpilih' : '' }}
                    </span>
                    <h3 class="font-display font-extrabold text-white text-xs sm:text-lg lg:text-xl leading-snug line-clamp-2">
                        {{ $category->name }}
                    </h3>
                    <p class="text-white/80 text-[10px] sm:text-xs font-medium mt-0.5 sm:mt-1 truncate block">
                        {{ $cardConfig['subtitle'] }}
                    </p>
                </div>

                <!-- Bottom Cutout PNG with Multi-layer Ambient Glow & Natural Floor Shadow -->
                <div class="relative z-10 w-full flex flex-col items-center justify-end">
                    <!-- Soft Radial Ambient Light Behind Product -->
                    <div class="absolute bottom-2 sm:bottom-4 w-20 sm:w-32 h-20 sm:h-32 rounded-full bg-white/15 blur-xl pointer-events-none"></div>

                    <!-- Realistic Floor Contact Shadow -->
                    <div class="absolute bottom-0 w-16 sm:w-28 h-2 sm:h-3.5 bg-black/35 rounded-[50%] blur-sm sm:blur-md pointer-events-none transform group-hover:scale-95 group-hover:opacity-60 transition-all duration-300"></div>

                    <!-- Cutout Image with Soft Dual-Tone Shadow -->
                    <div class="relative w-28 h-24 sm:w-40 sm:h-36 -mb-1 sm:-mb-2 flex items-center justify-center transform group-hover:scale-108 group-hover:-translate-y-1.5 transition-all duration-300 pointer-events-none">
                        <img src="{{ asset($cardConfig['image']) }}"
                             alt="{{ $category->name }}"
                             class="max-w-full max-h-full object-contain filter drop-shadow-[0_8px_10px_rgba(0,0,0,0.25)] drop-shadow-[0_2px_4px_rgba(0,0,0,0.15)]">
                    </div>
                </div>

                <!-- Corner Ambient Highlight -->
                <div class="absolute -top-10 -right-10 w-28 sm:w-36 h-28 sm:h-36 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
            </a>
        @endforeach
    </div>
</section>
@endif

<!-- Filter Bar & Search Stats -->
<section class="mb-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
        <div>
            <h2 class="font-display text-xl font-bold text-stone-900 tracking-tight">Katalog Pilihan</h2>
            <p class="text-xs text-stone-500 mt-0.5">Ditemukan <strong class="text-stone-800">{{ $products->total() }}</strong> produk siap pesan</p>
        </div>

        <!-- Filter Controls -->
        <form method="GET" action="{{ route('catalog.index') }}" class="flex flex-wrap items-center gap-2">
            @if(request('q'))
                <input type="hidden" name="q" value="{{ request('q') }}">
            @endif

            <select name="category" onchange="this.form.submit()" class="bg-white border border-stone-200 text-stone-700 text-xs font-semibold rounded-xl px-3 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-600">
                <option value="">Semua Kategori</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->slug }}" @selected(request('category') === $cat->slug)>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>

            <select name="sort" onchange="this.form.submit()" class="bg-white border border-stone-200 text-stone-700 text-xs font-semibold rounded-xl px-3 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-600">
                <option value="terbaru" @selected(request('sort') === 'terbaru')>Terbaru</option>
                <option value="termurah" @selected(request('sort') === 'termurah')>Harga Terendah</option>
                <option value="termahal" @selected(request('sort') === 'termahal')>Harga Tertinggi</option>
            </select>

            @if (request()->hasAny(['q', 'category', 'sort']))
                <a href="{{ route('catalog.index') }}" class="text-xs font-semibold text-rose-600 hover:text-rose-700 bg-rose-50 border border-rose-200 px-3 py-2 rounded-xl transition">
                    Reset Filter
                </a>
            @endif
        </form>
    </div>
</section>

<!-- Products Grid (Matching Home) -->
<section>
    @if ($products->isEmpty())
        <div class="bg-white border border-stone-200 rounded-2xl py-16 text-center text-stone-400 text-sm shadow-sm">
            <svg class="w-10 h-10 text-stone-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <p class="font-semibold text-stone-600">Tidak ada produk yang cocok</p>
            <p class="text-xs text-stone-400 mt-1">Coba gunakan kata kunci pencarian yang lebih umum atau reset filter.</p>
        </div>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 sm:gap-6">
            @foreach ($products as $product)
                <div class="group bg-white border border-stone-200/90 rounded-2xl overflow-hidden hover:border-brand-600/40 hover:shadow-md transition flex flex-col justify-between">
                    <div>
                        <!-- Product Image Container with Link -->
                        <a href="{{ route('catalog.show', $product->slug) }}" class="block relative w-full aspect-square bg-stone-100 overflow-hidden border-b border-stone-100">
                            @if ($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center text-stone-400 p-3 sm:p-4 text-center">
                                    <svg class="w-6 h-6 sm:w-8 sm:h-8 text-stone-300 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span class="text-[10px] sm:text-[11px] font-medium">Foto Produk</span>
                                </div>
                            @endif
                            <span class="absolute bottom-2 left-2 bg-stone-900/80 backdrop-blur-sm text-stone-200 text-[9px] sm:text-[10px] font-semibold px-1.5 py-0.5 rounded">
                                {{ $product->store->city }}
                            </span>
                        </a>

                        <!-- Card Details -->
                        <div class="p-3 sm:p-4">
                            <a href="{{ route('catalog.store', $product->store->slug) }}" class="text-[10px] sm:text-[11px] font-semibold uppercase tracking-wider text-brand-700 hover:underline mb-0.5 sm:mb-1 block truncate">
                                {{ $product->store->name }}
                            </a>
                            <a href="{{ route('catalog.show', $product->slug) }}">
                                <h3 class="font-display text-xs sm:text-sm font-bold text-stone-900 line-clamp-2 leading-snug group-hover:text-brand-700 transition min-h-[2rem] sm:min-h-[2.5rem]">
                                    {{ $product->name }}
                                </h3>
                            </a>
                        </div>
                    </div>

                    <!-- Price & Quick Add Button -->
                    <div class="p-3 sm:p-4 pt-0">
                        <div class="pt-2 sm:pt-3 border-t border-stone-100 flex items-center justify-between gap-1 sm:gap-2">
                            <div class="min-w-0 flex-1">
                                <span class="font-display text-xs sm:text-base font-extrabold text-stone-900 block leading-tight truncate">
                                    Rp {{ number_format((float) $product->price, 0, ',', '.') }}
                                </span>
                                <span class="text-[9px] sm:text-[10px] text-stone-400 font-medium block truncate">Sisa {{ $product->stock }}</span>
                            </div>

                            @auth
                                @if (auth()->user()->isCustomer())
                                    <form method="POST" action="{{ route('cart.add', $product) }}" class="shrink-0">
                                        @csrf
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="bg-brand-700 hover:bg-brand-800 text-white font-bold text-xs h-8 px-2.5 sm:px-3 sm:py-1.5 rounded-lg sm:rounded-xl transition shadow-sm active:scale-95 flex items-center justify-center gap-1">
                                            <span>+</span>
                                            <span class="hidden sm:inline">Tambah</span>
                                        </button>
                                    </form>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="text-brand-700 hover:bg-brand-50 border border-brand-200 font-bold text-xs h-8 px-2 sm:px-2.5 flex items-center justify-center rounded-lg sm:rounded-xl transition shrink-0">
                                    + <span class="hidden sm:inline ml-0.5">Tambah</span>
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $products->links() }}
        </div>
    @endif
</section>
@endsection
