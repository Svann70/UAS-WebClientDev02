@extends('layouts.app')

@section('title', $product->name)

@section('content')
<nav class="text-xs text-gray-500 mb-4">
    <a href="{{ route('home') }}" class="hover:underline">Beranda</a> /
    <a href="{{ route('catalog.index') }}" class="hover:underline">Katalog</a> /
    <a href="{{ route('catalog.store', $product->store->slug) }}" class="hover:underline">{{ $product->store->name }}</a> /
    <span class="text-gray-700">{{ $product->name }}</span>
</nav>

<div class="bg-white border border-gray-200 rounded-lg p-6 grid md:grid-cols-2 gap-8">
    <div>
        @if ($product->image)
            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                 class="w-full rounded-lg border border-gray-200 object-cover">
        @else
            <div class="w-full aspect-square bg-gray-100 rounded-lg flex items-center justify-center text-gray-400 text-sm">
                Tanpa foto
            </div>
        @endif
    </div>

    <div>
        <p class="text-sm text-emerald-700 mb-1">{{ $product->category->name }}</p>
        <h1 class="text-2xl font-bold mb-2">{{ $product->name }}</h1>
        <p class="text-2xl font-bold text-emerald-700 mb-4">
            Rp {{ number_format((float) $product->price, 0, ',', '.') }}
        </p>

        <div class="text-sm text-gray-600 space-y-1 mb-5">
            <p>Stok tersedia: <span class="font-medium">{{ $product->stock }}</span></p>
            <p>Dijual oleh:
                <a href="{{ route('catalog.store', $product->store->slug) }}"
                   class="text-emerald-700 hover:underline font-medium">{{ $product->store->name }}</a>
            </p>
            <p>Lokasi: {{ $product->store->city }}</p>
        </div>

        @auth
            @if (auth()->user()->isCustomer())
                @if ($product->stock > 0)
                    <form method="POST" action="{{ route('cart.add', $product->id) }}" class="flex gap-3 items-end">
                        @csrf
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Jumlah</label>
                            <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}"
                                   class="w-24 border border-gray-300 rounded px-3 py-2 text-sm">
                        </div>
                        <button type="submit"
                                class="bg-emerald-600 text-white px-6 py-2 rounded text-sm font-medium hover:bg-emerald-700">
                            Tambah ke Keranjang
                        </button>
                    </form>
                @else
                    <p class="bg-gray-100 text-gray-600 px-4 py-3 rounded text-sm">Stok habis.</p>
                @endif
            @endif
        @else
            <a href="{{ route('login') }}"
               class="inline-block bg-emerald-600 text-white px-6 py-2 rounded text-sm font-medium hover:bg-emerald-700">
                Masuk untuk membeli
            </a>
        @endauth
    </div>
</div>

<section class="mt-8">
    <h2 class="text-sm font-bold text-gray-700 mb-3">Deskripsi Produk</h2>
    {{-- nl2br + e() -> baris baru jadi <br>, tapi HTML dari user tetap di-escape (anti XSS). --}}
    <div class="bg-white border border-gray-200 rounded-lg p-5 text-sm text-gray-700 leading-relaxed">
        {!! nl2br(e($product->description)) !!}
    </div>
</section>

@if ($lainnya->isNotEmpty())
<section class="mt-8">
    <h2 class="text-lg font-bold mb-3">Produk Lain dari {{ $product->store->name }}</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach ($lainnya as $item)
            <a href="{{ route('catalog.show', $item->slug) }}"
               class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition">
                @if ($item->image)
                    <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="w-full h-32 object-cover">
                @else
                    <div class="w-full h-32 bg-gray-100 flex items-center justify-center text-gray-400 text-xs">Tanpa foto</div>
                @endif
                <div class="p-3">
                    <p class="text-sm font-medium line-clamp-2 mb-1">{{ $item->name }}</p>
                    <p class="text-sm font-bold">Rp {{ number_format((float) $item->price, 0, ',', '.') }}</p>
                </div>
            </a>
        @endforeach
    </div>
</section>
@endif
@endsection