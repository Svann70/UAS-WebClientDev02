@extends('layouts.app')

@section('title', 'Detail Pesanan')

@section('content')
@php
    $label = [
        'pending' => 'Menunggu Pembayaran',
        'paid' => 'Sudah Dibayar',
        'processing' => 'Diproses',
        'shipped' => 'Dikirim',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
    ];
@endphp

<a href="{{ route('orders.index') }}" class="text-sm text-gray-600 hover:underline">← Kembali ke daftar pesanan</a>

<div class="bg-white border border-gray-200 rounded-lg p-5 mt-4 mb-5">
    <div class="flex items-start justify-between mb-4">
        <div>
            <p class="font-mono text-lg font-bold">{{ $order->order_code }}</p>
            <p class="text-sm text-gray-500">{{ $order->created_at->format('d M Y, H:i') }}</p>
        </div>
        <span class="text-sm font-medium px-3 py-1 rounded bg-gray-100">{{ $label[$order->status] ?? $order->status }}</span>
    </div>

    <div class="grid md:grid-cols-2 gap-4 text-sm">
        <div>
            <p class="text-xs text-gray-500 mb-1">Alamat Pengiriman</p>
            <p class="whitespace-pre-line">{{ $order->shipping_address }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-500 mb-1">Total Pembayaran</p>
            <p class="text-xl font-bold text-emerald-700">Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}</p>
        </div>
    </div>

    @if ($order->notes)
        <div class="mt-3 text-sm">
            <p class="text-xs text-gray-500 mb-1">Catatan</p>
            <p class="text-gray-700">{{ $order->notes }}</p>
        </div>
    @endif
</div>

<div class="bg-white border border-gray-200 rounded-lg overflow-hidden mb-5">
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
                    <td class="px-4 py-3">
                        @if ($item->product)
                            <a href="{{ route('catalog.show', $item->product->slug) }}" class="font-medium hover:underline">
                                {{ $item->product->name }}
                            </a>
                        @else
                            <span class="text-gray-400">Produk telah dihapus</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $item->store->name }}</td>
                    <td class="px-4 py-3">Rp {{ number_format((float) $item->unit_price, 0, ',', '.') }}</td>
                    <td class="px-4 py-3">{{ $item->quantity }}</td>
                    <td class="px-4 py-3 text-right font-medium">Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@if ($order->status === 'pending')
    <div class="bg-white border border-gray-200 rounded-lg p-5">
        <h2 class="text-sm font-bold mb-2">Instruksi Pembayaran</h2>
        <div class="text-sm text-gray-700 mb-4 space-y-1">
            <p>Transfer ke rekening berikut:</p>
            <p class="font-mono bg-gray-50 rounded px-3 py-2 inline-block">
                BCA 1234567890 a.n. GANDENG Nusantara
            </p>
            <p>Nominal: <span class="font-bold">Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}</span></p>
        </div>

        <form method="POST" action="{{ route('orders.receipt', $order->order_code) }}" enctype="multipart/form-data" class="space-y-3">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-1">Unggah Bukti Transfer</label>
                <input type="file" name="payment_receipt" accept="image/jpeg,image/png,image/webp" required
                       class="w-full text-sm border border-gray-300 rounded px-3 py-2 file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:bg-emerald-50 file:text-emerald-700">
                <p class="text-xs text-gray-400 mt-1">Format JPG, PNG, atau WEBP. Maksimal 2 MB.</p>
            </div>
            <button type="submit" class="bg-emerald-600 text-white px-5 py-2 rounded text-sm font-medium hover:bg-emerald-700">
                Kirim Bukti Pembayaran
            </button>
        </form>
    </div>
@elseif ($order->payment_receipt)
    <div class="bg-white border border-gray-200 rounded-lg p-5">
        <h2 class="text-sm font-bold mb-2">Bukti Pembayaran</h2>
        <img src="{{ asset('storage/' . $order->payment_receipt) }}" alt="Bukti pembayaran"
             class="max-w-xs rounded border border-gray-200">
    </div>
@endif
@endsection