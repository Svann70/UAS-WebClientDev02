@extends('layouts.app')

@section('title', 'Pesanan Saya')

@section('content')
<h1 class="text-xl font-bold mb-4">Pesanan Saya</h1>

@php
    $badge = [
        'pending' => 'bg-amber-100 text-amber-800',
        'paid' => 'bg-blue-100 text-blue-800',
        'processing' => 'bg-indigo-100 text-indigo-800',
        'shipped' => 'bg-cyan-100 text-cyan-800',
        'completed' => 'bg-emerald-100 text-emerald-800',
        'cancelled' => 'bg-red-100 text-red-800',
    ];
    $label = [
        'pending' => 'Menunggu Pembayaran',
        'paid' => 'Sudah Dibayar',
        'processing' => 'Diproses',
        'shipped' => 'Dikirim',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
    ];
@endphp

@if ($orders->isEmpty())
    <div class="bg-white border border-gray-200 rounded-lg py-12 text-center">
        <p class="text-gray-400 text-sm mb-4">Belum ada pesanan.</p>
        <a href="{{ route('catalog.index') }}"
           class="inline-block bg-emerald-600 text-white px-5 py-2 rounded text-sm hover:bg-emerald-700">Mulai Belanja</a>
    </div>
@else
    <div class="space-y-3">
        @foreach ($orders as $order)
            <a href="{{ route('orders.show', $order->order_code) }}"
               class="block bg-white border border-gray-200 rounded-lg p-4 hover:shadow-sm transition">
                <div class="flex items-center justify-between mb-1">
                    <span class="font-mono text-sm font-medium">{{ $order->order_code }}</span>
                    <span class="text-xs px-2 py-0.5 rounded {{ $badge[$order->status] ?? 'bg-gray-100 text-gray-700' }}">
                        {{ $label[$order->status] ?? $order->status }}
                    </span>
                </div>
                <div class="flex items-center justify-between text-sm text-gray-500">
                    <span>{{ $order->items_count }} item · {{ $order->created_at->format('d M Y H:i') }}</span>
                    <span class="font-bold text-gray-800">Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}</span>
                </div>
            </a>
        @endforeach
    </div>
    <div class="mt-4">{{ $orders->links() }}</div>
@endif
@endsection