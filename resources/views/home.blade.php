@extends('layouts.app')

@section('title', 'GANDENG — Gerakan Anak Nusantara Dagang')

@section('content')
<section class="bg-emerald-700 text-white rounded-lg px-6 py-10 mb-8">
    <h1 class="text-2xl font-bold mb-2">Produk UMKM Lokal, Langsung dari Pengrajinnya</h1>
    <p class="text-emerald-100 text-sm mb-5 max-w-xl">
        GANDENG menghubungkan pembeli dengan pemilik usaha daerah. Setiap pembelian Anda
        mendukung perekonomian anak Nusantara.
    </p>
    <form method="GET" action="{{ route('catalog.index') }}" class="flex gap-2 max-w-lg">
        <input type="text" name="q" placeholder="Cari produk UMKM..."
               class="flex-1 rounded px-4 py-2 text-sm text-gray-800 focus:outline-none">
        <button type="submit" class="bg-white text-emerald-700 px-4 py-2 rounded text-sm font-medium hover:bg-emerald-50">
            Cari
        </button>
    </form>
</section>

@if ($categories->isNotEmpty())
<section class="mb-8">
    <h2 class="text-lg font-bold mb-3">Kategori</h2>
    <div class="flex flex-wrap gap-2">
        @foreach ($categories as $category)
            <a href="{{ route('catalog.index', ['category' => $category->slug]) }}"
               class="bg-white border border-gray-200 rounded-full px-4 py-1.5 text-sm hover:border-emerald-500 hover:text-emerald-700">
                {{ $category->name }}
                <span class="text-gray-400 text-xs">({{ $category->products_count }})</span>
            </a>
        @endforeach
    </div>
</section>
@endif

<section>
    <div class="flex items-center justify-between mb-3">
        <h2 class="text-lg font-bold">Produk Terbaru</h2>
        <a href="{{ route('catalog.index') }}" class="text-sm text-emerald-700 hover:underline">Lihat semua</a>
    </div>

    @if ($products->isEmpty())
        <div class="bg-white border border-gray-200 rounded-lg py-12 text-center text-gray-400 text-sm">
            Belum ada produk yang tersedia.
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
                        <div class="w-full h-36 bg-gray-100 flex items-center justify-center text-gray-400 text-xs">
                            Tanpa foto
                        </div>
                    @endif
                    <div class="p-3">
                        <p class="text-xs text-emerald-700 mb-1">{{ $product->store->name }}</p>
                        <p class="text-sm font-medium line-clamp-2 mb-1">{{ $product->name }}</p>
                        <p class="text-sm font-bold">Rp {{ number_format((float) $product->price, 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-400 mt-1">Stok {{ $product->stock }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</section>
@endsection