@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<h1 class="text-xl font-bold mb-4">Checkout</h1>

<div class="grid md:grid-cols-3 gap-6">
    <div class="md:col-span-2">
        <form method="POST" action="{{ route('checkout.process') }}" class="bg-white border border-gray-200 rounded-lg p-5 space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium mb-1">Alamat Pengiriman</label>
                <textarea name="shipping_address" rows="3" required maxlength="500"
                          placeholder="Nama penerima, alamat lengkap, kota, kode pos"
                          class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('shipping_address', $defaultAddress) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Catatan <span class="text-gray-400">(opsional)</span></label>
                <textarea name="notes" rows="2" maxlength="500"
                          placeholder="mis. titip di pos keamanan"
                          class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('notes') }}</textarea>
            </div>

            <div class="bg-amber-50 border border-amber-200 rounded p-3 text-xs text-amber-800">
                Pembayaran dilakukan via transfer bank. Setelah pesanan dibuat, Anda akan mendapat
                kode pesanan dan instruksi untuk mengunggah bukti transfer.
            </div>

            <button type="submit"
                    class="w-full bg-emerald-600 text-white py-2.5 rounded text-sm font-medium hover:bg-emerald-700">
                Buat Pesanan
            </button>
        </form>
    </div>

    <div>
        <div class="bg-white border border-gray-200 rounded-lg p-5">
            <h2 class="text-sm font-bold mb-3">Ringkasan Pesanan</h2>
            <ul class="space-y-3 text-sm">
                @foreach ($items as $item)
                    <li class="flex justify-between gap-3">
                        <div>
                            <p class="font-medium">{{ $item['product']->name }}</p>
                            <p class="text-xs text-gray-500">{{ $item['product']->store->name }} · {{ $item['qty'] }}x</p>
                        </div>
                        <span class="whitespace-nowrap">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                    </li>
                @endforeach
            </ul>
            <div class="border-t border-gray-200 mt-3 pt-3 flex justify-between text-sm font-bold">
                <span>Total</span>
                <span class="text-emerald-700">Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
</div>
@endsection