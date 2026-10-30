@extends('layouts.app')

@section('title', 'Dashboard Merchant')

@section('content')
<div class="flex items-center justify-between mb-5">
    <div>
        <h1 class="text-xl font-bold">{{ $store->name }}</h1>
        <p class="text-sm text-gray-500">{{ $store->city }} · {{ $store->status === 'active' ? 'Toko Aktif' : ucfirst($store->status) }}</p>
    </div>
    <div class="flex gap-2 text-sm">
        <a href="{{ route('catalog.store', $store->slug) }}" target="_blank"
           class="border border-gray-300 px-3 py-1.5 rounded hover:bg-gray-50">Lihat Toko</a>
        <a href="{{ route('merchant.products.create') }}"
           class="bg-emerald-600 text-white px-3 py-1.5 rounded hover:bg-emerald-700">+ Produk</a>
    </div>
</div>

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white border border-gray-200 rounded-lg p-4">
        <p class="text-xs text-gray-500 mb-1">Total Produk</p>
        <p class="text-2xl font-bold">{{ $stats['total_produk'] }}</p>
        <p class="text-xs text-gray-400 mt-1">{{ $stats['produk_aktif'] }} aktif</p>
    </div>
    <div class="bg-white border border-gray-200 rounded-lg p-4">
        <p class="text-xs text-gray-500 mb-1">Omzet</p>
        <p class="text-2xl font-bold text-emerald-700">Rp {{ number_format((float) $omzet, 0, ',', '.') }}</p>
        <p class="text-xs text-gray-400 mt-1">Pesanan sudah dibayar</p>
    </div>
    <div class="bg-white border border-gray-200 rounded-lg p-4">
        <p class="text-xs text-gray-500 mb-1">Perlu Diproses</p>
        <p class="text-2xl font-bold {{ $stats['pesanan_masuk'] > 0 ? 'text-amber-600' : '' }}">{{ $stats['pesanan_masuk'] }}</p>
        <p class="text-xs text-gray-400 mt-1">Sudah dibayar, belum dikirim</p>
    </div>
    <div class="bg-white border border-gray-200 rounded-lg p-4">
        <p class="text-xs text-gray-500 mb-1">Stok Menipis</p>
        <p class="text-2xl font-bold {{ $stats['stok_menipis'] > 0 ? 'text-red-600' : '' }}">{{ $stats['stok_menipis'] }}</p>
        <p class="text-xs text-gray-400 mt-1">Sisa ≤ 5 unit</p>
    </div>
</div>

<div class="grid md:grid-cols-2 gap-5">
    <div class="bg-white border border-gray-200 rounded-lg p-5">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-sm font-bold">Produk Perlu Perhatian</h2>
            <a href="{{ route('merchant.products.index') }}" class="text-xs text-emerald-700 hover:underline">Semua produk</a>
        </div>
        @if ($perhatian->isEmpty())
            <p class="text-sm text-gray-400 py-4 text-center">Semua stok produk aman.</p>
        @else
            <ul class="divide-y divide-gray-100 text-sm">
                @foreach ($perhatian as $p)
                    <li class="py-2 flex items-center justify-between gap-3">
                        <a href="{{ route('merchant.products.edit', $p) }}" class="hover:underline truncate">{{ $p->name }}</a>
                        <span class="whitespace-nowrap {{ $p->stock === 0 ? 'text-red-600 font-medium' : 'text-amber-600' }}">
                            {{ $p->stock === 0 ? 'Habis' : $p->stock . ' unit' }}
                        </span>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    <div class="bg-white border border-gray-200 rounded-lg p-5">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-sm font-bold">Pesanan Terbaru</h2>
            <a href="{{ route('merchant.orders.index') }}" class="text-xs text-emerald-700 hover:underline">Semua pesanan</a>
        </div>
        @if ($terbaru->isEmpty())
            <p class="text-sm text-gray-400 py-4 text-center">Belum ada pesanan masuk.</p>
        @else
            <ul class="divide-y divide-gray-100 text-sm">
                @foreach ($terbaru as $item)
                    <li class="py-2">
                        <div class="flex items-center justify-between gap-3">
                            <span class="font-mono text-xs">{{ $item->order->order_code }}</span>
                            <span class="text-xs text-gray-500">{{ $item->order->created_at->format('d/m H:i') }}</span>
                        </div>
                        <p class="text-xs text-gray-600 mt-0.5">
                            {{ $item->product->name ?? 'Produk dihapus' }} · {{ $item->quantity }}x
                        </p>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
@endsection