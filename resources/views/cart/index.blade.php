@extends('layouts.app')

@section('title', 'Keranjang Belanja')

@section('content')
<div class="mb-6">
    <h1 class="font-display text-2xl font-extrabold text-stone-900 tracking-tight">Keranjang Belanja</h1>
    <p class="text-xs text-stone-500 mt-1">Periksa kembali item pesanan sebelum menuju halaman checkout.</p>
</div>

@if (empty($items))
    <div class="bg-white border border-stone-200/90 rounded-2xl py-16 text-center shadow-sm">
        <svg class="w-12 h-12 text-stone-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
        <p class="font-display font-bold text-stone-700 text-base mb-1">Keranjang Masih Kosong</p>
        <p class="text-xs text-stone-400 mb-6 max-w-sm mx-auto">Anda belum menambahkan panganan atau produk kerajinan ke dalam keranjang.</p>
        <a href="{{ route('catalog.index') }}"
           class="inline-block bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-6 py-2.5 rounded-lg text-xs transition shadow-sm">
            Eksplorasi Katalog Produk &rarr;
        </a>
    </div>
@else
    <div class="bg-white border border-stone-200/90 rounded-2xl overflow-hidden shadow-sm mb-6">
        <table class="w-full text-sm">
            <thead class="bg-stone-50 text-left text-stone-600 text-xs font-bold uppercase tracking-wider border-b border-stone-200/80">
                <tr>
                    <th class="px-5 py-3.5">Rincian Produk</th>
                    <th class="px-5 py-3.5">Harga</th>
                    <th class="px-5 py-3.5">Jumlah</th>
                    <th class="px-5 py-3.5">Subtotal</th>
                    <th class="px-5 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @foreach ($items as $item)
                    <tr class="hover:bg-stone-50/50 transition">
                        <td class="px-5 py-4">
                            <a href="{{ route('catalog.show', $item['product']->slug) }}" class="font-display font-bold text-stone-900 hover:text-emerald-700 transition">
                                {{ $item['product']->name }}
                            </a>
                            <p class="text-xs text-stone-500 mt-0.5">Toko: {{ $item['product']->store->name }}</p>
                        </td>
                        <td class="px-5 py-4 font-semibold text-stone-700">
                            Rp {{ number_format((float) $item['product']->price, 0, ',', '.') }}
                        </td>
                        <td class="px-5 py-4">
                            <form method="POST" action="{{ route('cart.update', $item['product']->id) }}" class="flex items-center gap-2">
                                @csrf
                                @method('PATCH')
                                <input type="number" name="quantity" value="{{ $item['qty'] }}"
                                       min="0" max="{{ $item['product']->stock }}"
                                       class="w-16 bg-stone-50 border border-stone-300 rounded px-2.5 py-1 text-xs font-bold text-stone-800 text-center">
                                <button type="submit" class="text-xs font-semibold text-emerald-700 hover:underline">
                                    Ubah
                                </button>
                            </form>
                            <p class="text-[10px] text-stone-400 mt-1">Stok {{ $item['product']->stock }}</p>
                        </td>
                        <td class="px-5 py-4 font-display font-bold text-stone-900">
                            Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                        </td>
                        <td class="px-5 py-4 text-right">
                            <form method="POST" action="{{ route('cart.remove', $item['product']->id) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-600 font-semibold text-xs hover:underline">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-stone-50 border-t border-stone-200">
                <tr>
                    <td colspan="3" class="px-5 py-4 text-right font-display font-bold text-stone-700 text-sm">
                        Total Pembayaran
                    </td>
                    <td colspan="2" class="px-5 py-4 font-display text-lg font-black text-emerald-700">
                        Rp {{ number_format($total, 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
        <a href="{{ route('catalog.index') }}" class="text-xs font-semibold text-stone-600 hover:text-stone-900 flex items-center gap-1">
            <span>&larr;</span>
            <span>Tambah Belanjaan Lain</span>
        </a>
        <a href="{{ route('checkout.index') }}"
           class="w-full sm:w-auto bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-8 py-3 rounded-lg text-sm transition shadow-sm text-center">
            Lanjut ke Alamat Pengiriman &rarr;
        </a>
    </div>
@endif
@endsection