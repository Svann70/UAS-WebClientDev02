@extends('layouts.app')

@section('title', 'Monitoring Pesanan')

@section('content')
@php
    $label = [
        'pending' => 'Menunggu Bayar', 'paid' => 'Sudah Dibayar', 'processing' => 'Diproses',
        'shipped' => 'Dikirim', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan',
    ];
    $badge = [
        'pending' => 'bg-amber-100 text-amber-800', 'paid' => 'bg-blue-100 text-blue-800',
        'processing' => 'bg-indigo-100 text-indigo-800', 'shipped' => 'bg-cyan-100 text-cyan-800',
        'completed' => 'bg-emerald-100 text-emerald-800', 'cancelled' => 'bg-red-100 text-red-800',
    ];
@endphp

<h1 class="text-xl font-bold mb-1">Monitoring Pesanan</h1>
<p class="text-sm text-gray-500 mb-4">Seluruh transaksi lintas mitra.</p>

<form method="GET" class="flex flex-wrap gap-2 items-center mb-4 text-sm">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari kode pesanan"
           class="border border-gray-300 rounded px-3 py-1.5 w-56">
    <select name="status" class="border border-gray-300 rounded px-3 py-1.5">
        <option value="">Semua status</option>
        @foreach ($label as $key => $text)
            <option value="{{ $key }}" @selected(request('status') === $key)>{{ $text }}</option>
        @endforeach
    </select>
    <button type="submit" class="bg-emerald-600 text-white px-3 py-1.5 rounded hover:bg-emerald-700">Filter</button>
    @if (request()->hasAny(['q', 'status']))
        <a href="{{ route('admin.orders.index') }}" class="text-gray-500 hover:underline">Reset</a>
    @endif
</form>

@if ($orders->isEmpty())
    <div class="bg-white border border-gray-200 rounded-lg py-12 text-center text-gray-400 text-sm">
        Tidak ada pesanan yang cocok.
    </div>
@else
    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-600">
                <tr>
                    <th class="px-4 py-3 font-medium">Kode</th>
                    <th class="px-4 py-3 font-medium">Pembeli</th>
                    <th class="px-4 py-3 font-medium">Item</th>
                    <th class="px-4 py-3 font-medium">Total</th>
                    <th class="px-4 py-3 font-medium">Status</th>
                    <th class="px-4 py-3 font-medium">Tanggal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($orders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.orders.show', $order) }}" class="font-mono text-xs font-medium hover:underline">
                                {{ $order->order_code }}
                            </a>
                        </td>
                        <td class="px-4 py-3">{{ $order->user->name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $order->items_count }}</td>
                        <td class="px-4 py-3 font-medium">Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">
                            <span class="text-xs px-2 py-0.5 rounded {{ $badge[$order->status] ?? 'bg-gray-100' }}">
                                {{ $label[$order->status] ?? $order->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $order->created_at->format('d M Y H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $orders->links() }}</div>
@endif
@endsection