@extends('layouts.app')

@section('title', 'Detail Pesanan')

@section('content')
@php
    $label = [
        'pending' => 'Menunggu Bayar', 'paid' => 'Sudah Dibayar', 'processing' => 'Diproses',
        'shipped' => 'Dikirim', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan',
    ];
@endphp

<a href="{{ route('admin.orders.index') }}" class="text-sm text-gray-600 hover:underline">← Kembali</a>

<div class="bg-white border border-gray-200 rounded-lg p-5 mt-4 mb-5">
    <div class="flex items-start justify-between mb-4">
        <div>
            <p class="font-mono text-lg font-bold">{{ $order->order_code }}</p>
            <p class="text-sm text-gray-500">{{ $order->created_at->format('d M Y, H:i') }}</p>
        </div>
        <span class="text-sm px-3 py-1 rounded bg-gray-100">{{ $label[$order->status] ?? $order->status }}</span>
    </div>

    <div class="grid md:grid-cols-3 gap-4 text-sm">
        <div>
            <p class="text-xs text-gray-500 mb-1">Pembeli</p>
            <p class="font-medium">{{ $order->user->name }}</p>
            <p class="text-xs text-gray-500">{{ $order->user->email }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-500 mb-1">Alamat Pengiriman</p>
            <p class="whitespace-pre-line">{{ $order->shipping_address }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-500 mb-1">Total</p>
            <p class="text-xl font-bold text-emerald-700">Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}</p>
        </div>
    </div>

    @if ($order->payment_receipt)
        <div class="mt-4">
            <p class="text-xs text-gray-500 mb-1">Bukti Pembayaran</p>
            <a href="{{ asset('storage/' . $order->payment_receipt) }}" target="_blank">
                <img src="{{ asset('storage/' . $order->payment_receipt) }}" alt="Bukti pembayaran"
                     class="max-w-[200px] rounded border border-gray-200">
            </a>
        </div>
    @endif
</div>

<div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left text-gray-600">
            <tr>
                <th class="px-4 py-3 font-medium">Produk</th>
                <th class="px-4 py-3 font-medium">Toko</th>
                <th class="px-4 py-3 font-medium">Harga</th>
                <th class="px-4 py-3 font-medium">Qty</th>
                <th class="px-4 py-3 font-medium text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach ($order->items as $item)
                <tr>
                    <td class="px-4 py-3">{{ $item->product->name ?? 'Produk dihapus' }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $item->store->name }}</td>
                    <td class="px-4 py-3">Rp {{ number_format((float) $item->unit_price, 0, ',', '.') }}</td>
                    <td class="px-4 py-3">{{ $item->quantity }}</td>
                    <td class="px-4 py-3 text-right font-medium">Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection