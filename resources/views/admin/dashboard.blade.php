@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<h1 class="text-xl font-bold mb-1">Dashboard Platform</h1>
<p class="text-sm text-gray-500 mb-5">Ringkasan seluruh aktivitas GANDENG.</p>

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white border border-gray-200 rounded-lg p-4">
        <p class="text-xs text-gray-500 mb-1">Mitra Aktif</p>
        <p class="text-2xl font-bold text-emerald-700">{{ $stats['mitra_aktif'] }}</p>
        @if ($stats['mitra_pending'] > 0)
            <a href="{{ route('admin.stores.index', ['status' => 'pending']) }}"
               class="text-xs text-amber-600 hover:underline mt-1 inline-block">
                {{ $stats['mitra_pending'] }} menunggu verifikasi
            </a>
        @else
            <p class="text-xs text-gray-400 mt-1">Tidak ada yang pending</p>
        @endif
    </div>
    <div class="bg-white border border-gray-200 rounded-lg p-4">
        <p class="text-xs text-gray-500 mb-1">Volume Transaksi</p>
        <p class="text-2xl font-bold text-emerald-700">Rp {{ number_format((float) $stats['transaksi'], 0, ',', '.') }}</p>
        <p class="text-xs text-gray-400 mt-1">Total pesanan lunas</p>
    </div>
    <div class="bg-white border border-gray-200 rounded-lg p-4">
        <p class="text-xs text-gray-500 mb-1">Total Pesanan</p>
        <p class="text-2xl font-bold">{{ $stats['pesanan'] }}</p>
        <p class="text-xs text-gray-400 mt-1">{{ $stats['belum_diproses'] }} sudah dibayar</p>
    </div>
    <div class="bg-white border border-gray-200 rounded-lg p-4">
        <p class="text-xs text-gray-500 mb-1">Produk & Pembeli</p>
        <p class="text-2xl font-bold">{{ $stats['produk'] }}</p>
        <p class="text-xs text-gray-400 mt-1">{{ $stats['customer'] }} akun pembeli</p>
    </div>
</div>

<div class="grid md:grid-cols-2 gap-5">
    <div class="bg-white border border-gray-200 rounded-lg p-5">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-sm font-bold">Mitra Terbaru</h2>
            <a href="{{ route('admin.stores.index') }}" class="text-xs text-emerald-700 hover:underline">Semua mitra</a>
        </div>
        @if ($tokoTerbaru->isEmpty())
            <p class="text-sm text-gray-400 py-4 text-center">Belum ada mitra terdaftar.</p>
        @else
            <ul class="divide-y divide-gray-100 text-sm">
                @foreach ($tokoTerbaru as $store)
                    <li class="py-2 flex items-center justify-between gap-2">
                        <div class="min-w-0">
                            <p class="font-medium truncate">{{ $store->name }}</p>
                            <p class="text-xs text-gray-500">{{ $store->city }} · {{ $store->user->name }}</p>
                        </div>
                        <span class="text-xs px-2 py-0.5 rounded whitespace-nowrap
                            {{ $store->status === 'active' ? 'bg-emerald-100 text-emerald-800' : ($store->status === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800') }}">
                            {{ ucfirst($store->status) }}
                        </span>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    <div class="bg-white border border-gray-200 rounded-lg p-5">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-sm font-bold">Pesanan Terbaru</h2>
            <a href="{{ route('admin.orders.index') }}" class="text-xs text-emerald-700 hover:underline">Semua pesanan</a>
        </div>
        @if ($pesananTerbaru->isEmpty())
            <p class="text-sm text-gray-400 py-4 text-center">Belum ada pesanan.</p>
        @else
            <ul class="divide-y divide-gray-100 text-sm">
                @foreach ($pesananTerbaru as $order)
                    <li class="py-2">
                        <div class="flex items-center justify-between gap-2">
                            <a href="{{ route('admin.orders.show', $order) }}" class="font-mono text-xs hover:underline">
                                {{ $order->order_code }}
                            </a>
                            <span class="font-medium whitespace-nowrap">Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5">
                            {{ $order->user->name }} · {{ $order->items_count }} item · {{ $order->status }}
                        </p>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>

<div class="bg-white border border-gray-200 rounded-lg p-5 mt-5">
    <div class="flex items-center justify-between mb-3">
        <h2 class="text-sm font-bold">Kategori Terbanyak</h2>
        <a href="{{ route('admin.categories.index') }}" class="text-xs text-emerald-700 hover:underline">Kelola kategori</a>
    </div>
    <ul class="divide-y divide-gray-100 text-sm">
        @forelse ($kategoriPopuler as $category)
            <li class="py-2 flex items-center justify-between">
                <span>{{ $category->name }}</span>
                <span class="text-xs text-gray-500">{{ $category->products_count }} produk</span>
            </li>
        @empty
            <li class="py-4 text-center text-gray-400">Belum ada kategori.</li>
        @endforelse
    </ul>
</div>
@endsection