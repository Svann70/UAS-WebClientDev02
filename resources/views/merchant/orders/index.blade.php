@extends('layouts.app')

@section('title', 'Pesanan Masuk')

@section('content')
@php
    $label = [
        'pending' => 'Menunggu Bayar',
        'paid' => 'Sudah Dibayar',
        'processing' => 'Diproses',
        'shipped' => 'Dikirim',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
    ];
    $badge = [
        'pending' => 'bg-amber-100 text-amber-800',
        'paid' => 'bg-blue-100 text-blue-800',
        'processing' => 'bg-indigo-100 text-indigo-800',
        'shipped' => 'bg-cyan-100 text-cyan-800',
        'completed' => 'bg-emerald-100 text-emerald-800',
        'cancelled' => 'bg-red-100 text-red-800',
    ];
    $next = ['paid' => 'processing', 'processing' => 'shipped', 'shipped' => 'completed'];
@endphp

<h1 class="text-xl font-bold mb-1">Pesanan Masuk</h1>
<p class="text-sm text-gray-500 mb-4">Hanya pesanan yang berisi produk toko Anda.</p>

<div class="grid grid-cols-2 gap-4 mb-5">
    <div class="bg-white border border-gray-200 rounded-lg p-4">
        <p class="text-xs text-gray-500 mb-1">Omzet Toko</p>
        <p class="text-xl font-bold text-emerald-700">Rp {{ number_format((float) $omzet, 0, ',', '.') }}</p>
    </div>
    <div class="bg-white border border-gray-200 rounded-lg p-4">
        <p class="text-xs text-gray-500 mb-1">Menunggu Diproses</p>
        <p class="text-xl font-bold {{ $menunggu > 0 ? 'text-amber-600' : '' }}">{{ $menunggu }}</p>
    </div>
</div>

<form method="GET" class="flex items-center gap-2 mb-4 text-sm">
    <select name="status" class="border border-gray-300 rounded px-3 py-1.5">
        <option value="">Semua status</option>
        @foreach ($label as $key => $text)
            <option value="{{ $key }}" @selected(request('status') === $key)>{{ $text }}</option>
        @endforeach
    </select>
    <button type="submit" class="bg-emerald-600 text-white px-3 py-1.5 rounded hover:bg-emerald-700">Filter</button>
    @if (request()->filled('status'))
        <a href="{{ route('merchant.orders.index') }}" class="text-gray-500 hover:underline">Reset</a>
    @endif
</form>

@if ($items->isEmpty())
    <div class="bg-white border border-gray-200 rounded-lg py-12 text-center text-gray-400 text-sm">
        Belum ada pesanan masuk.
    </div>
@else
    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-600">
                <tr>
                    <th class="px-4 py-3 font-medium">Kode Pesanan</th>
                    <th class="px-4 py-3 font-medium">Produk</th>
                    <th class="px-4 py-3 font-medium">Pembeli</th>
                    <th class="px-4 py-3 font-medium">Subtotal</th>
                    <th class="px-4 py-3 font-medium">Status</th>
                    <th class="px-4 py-3 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($items as $item)
                    <tr>
                        <td class="px-4 py-3">
                            <span class="font-mono text-xs">{{ $item->order->order_code }}</span>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $item->order->created_at->format('d M Y H:i') }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <p class="font-medium">{{ $item->product->name ?? 'Produk dihapus' }}</p>
                            <p class="text-xs text-gray-500">{{ $item->quantity }}x · Rp {{ number_format((float) $item->unit_price, 0, ',', '.') }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <p>{{ $item->order->user->name }}</p>
                            <p class="text-xs text-gray-500 whitespace-pre-line max-w-[180px] truncate">{{ $item->order->shipping_address }}</p>
                        </td>
                        <td class="px-4 py-3 font-medium">Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">
                            <span class="text-xs px-2 py-0.5 rounded {{ $badge[$item->order->status] ?? 'bg-gray-100 text-gray-700' }}">
                                {{ $label[$item->order->status] ?? $item->order->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if (isset($next[$item->order->status]))
                                <form method="POST" action="{{ route('merchant.orders.status', $item) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="{{ $next[$item->order->status] }}">
                                    <button type="submit" class="bg-emerald-600 text-white px-3 py-1 rounded text-xs hover:bg-emerald-700">
                                        Tandai {{ $label[$next[$item->order->status]] }}
                                    </button>
                                </form>
                            @else
                                <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $items->links() }}</div>
@endif
@endsection