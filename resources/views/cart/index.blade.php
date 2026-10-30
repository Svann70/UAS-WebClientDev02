@extends('layouts.app')

@section('title', 'Keranjang Belanja')

@section('content')
<h1 class="text-xl font-bold mb-4">Keranjang Belanja</h1>

@if (empty($items))
    <div class="bg-white border border-gray-200 rounded-lg py-12 text-center">
        <p class="text-gray-400 text-sm mb-4">Keranjang Anda masih kosong.</p>
        <a href="{{ route('catalog.index') }}"
           class="inline-block bg-emerald-600 text-white px-5 py-2 rounded text-sm hover:bg-emerald-700">
            Mulai Belanja
        </a>
    </div>
@else
    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-600">
                <tr>
                    <th class="px-4 py-3 font-medium">Produk</th>
                    <th class="px-4 py-3 font-medium">Harga</th>
                    <th class="px-4 py-3 font-medium">Jumlah</th>
                    <th class="px-4 py-3 font-medium">Subtotal</th>
                    <th class="px-4 py-3 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($items as $item)
                    <tr>
                        <td class="px-4 py-3">
                            <a href="{{ route('catalog.show', $item['product']->slug) }}" class="font-medium hover:underline">
                                {{ $item['product']->name }}
                            </a>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $item['product']->store->name }}</p>
                        </td>
                        <td class="px-4 py-3">Rp {{ number_format((float) $item['product']->price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">
                            <form method="POST" action="{{ route('cart.update', $item['product']->id) }}" class="flex items-center gap-2">
                                @csrf
                                @method('PATCH')
                                <input type="number" name="quantity" value="{{ $item['qty'] }}"
                                       min="0" max="{{ $item['product']->stock }}"
                                       class="w-16 border border-gray-300 rounded px-2 py-1 text-sm">
                                <button type="submit" class="text-xs text-emerald-700 hover:underline">Ubah</button>
                            </form>
                            <p class="text-xs text-gray-400 mt-1">Stok {{ $item['product']->stock }}</p>
                        </td>
                        <td class="px-4 py-3 font-medium">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right">
                            <form method="POST" action="{{ route('cart.remove', $item['product']->id) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 text-sm hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-50">
                <tr>
                    <td colspan="3" class="px-4 py-3 text-right font-medium">Total</td>
                    <td colspan="2" class="px-4 py-3 font-bold text-emerald-700">
                        Rp {{ number_format($total, 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="flex justify-between items-center mt-5">
        <a href="{{ route('catalog.index') }}" class="text-sm text-gray-600 hover:underline">← Lanjut belanja</a>
        <a href="{{ route('checkout.index') }}"
           class="bg-emerald-600 text-white px-6 py-2 rounded text-sm font-medium hover:bg-emerald-700">
            Lanjut Checkout
        </a>
    </div>
@endif
@endsection