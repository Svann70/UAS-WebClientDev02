@extends('layouts.app')

@section('title', 'GANDENG — Gerakan Anak Nusantara Dagang')

@section('content')
<!-- Hero Section (Editorial Warm Ground, Distinct Hierarchy, Anti-Slop copy) -->
<section class="bg-stone-900 text-stone-100 rounded-2xl p-8 sm:p-12 mb-12 border border-stone-800 shadow-sm">
    <div class="max-w-2xl">
        <p class="text-xs font-semibold text-stone-400 uppercase tracking-widest mb-3">
            Katalog Produk Pilihan Daerah
        </p>
        <h1 class="font-display text-3xl sm:text-5xl font-extrabold tracking-tight text-white mb-4 leading-[1.15]">
            Langsung dari Dapur & Pengrajin Nusantara.
        </h1>
        <p class="text-stone-300 text-sm sm:text-base mb-8 leading-relaxed">
            Beli panganan khas, kopi lereng gunung, dan sambal kemasan asli dari pemilik UMKM daerah. Setiap transaksi tercatat langsung ke kas toko mitra.
        </p>

        <!-- Search Bar with Solid Clear Hierarchy -->
        <form method="GET" action="{{ route('catalog.index') }}" class="flex flex-col sm:flex-row gap-2 max-w-xl">
            <div class="relative flex-1">
                <input type="text" name="q" placeholder="Cari keripik, biji kopi, sambal roa..."
                       class="w-full bg-white text-stone-900 placeholder:text-stone-400 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 shadow-inner font-medium">
            </div>
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-white font-semibold px-6 py-3 rounded-lg text-sm transition shadow-sm shrink-0">
                Cari Produk
            </button>
        </form>
    </div>
</section>

<!-- Categories Section (Warm Pill Navigation) -->
@if ($categories->isNotEmpty())
<section class="mb-12">
    <div class="flex items-baseline justify-between mb-4">
        <h2 class="font-display text-lg font-bold text-stone-900 tracking-tight">Kategori Produk</h2>
        <span class="text-xs text-stone-500 font-medium">Pilihan Pangan Tradisional</span>
    </div>
    <div class="flex flex-wrap gap-2.5">
        @foreach ($categories as $category)
            <a href="{{ route('catalog.index', ['category' => $category->slug]) }}"
               class="bg-white border border-stone-200/90 rounded-xl px-4 py-2.5 text-xs font-semibold text-stone-700 hover:border-emerald-600 hover:text-emerald-700 hover:bg-emerald-50/50 transition shadow-sm flex items-center gap-2">
                <span>{{ $category->name }}</span>
                <span class="bg-stone-100 text-stone-500 text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $category->products_count }}</span>
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
                <a href="{{ route('catalog.show', $product->slug) }}"
                   class="group bg-white border border-stone-200/90 rounded-xl overflow-hidden hover:border-stone-300 hover:shadow-md transition flex flex-col">
                    
                    <!-- Product Image Container with Solid Aspect Ratio -->
                    <div class="relative w-full aspect-square bg-stone-100 overflow-hidden border-b border-stone-100">
                        @if ($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center text-stone-400 p-4 text-center">
                                <svg class="w-8 h-8 text-stone-300 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span class="text-[11px] font-medium">Foto Produk</span>
                            </div>
                        @endif
                        <span class="absolute bottom-2 left-2 bg-stone-900/80 backdrop-blur-sm text-stone-200 text-[10px] font-semibold px-2 py-0.5 rounded">
                            {{ $product->store->city }}
                        </span>
                    </div>

                    <!-- Card Details -->
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
    @endif
</section>
@endsection