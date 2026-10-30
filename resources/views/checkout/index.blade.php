@extends('layouts.app')

@section('title', 'Alamat Pengiriman & Checkout')

@section('content')
<div class="mb-6">
    <h1 class="font-display text-2xl font-extrabold text-stone-900 tracking-tight">Checkout Pesanan</h1>
    <p class="text-xs text-stone-500 mt-1">Lengkapi alamat pengiriman untuk proses serah terima barang dari toko mitra.</p>
</div>

<div class="grid md:grid-cols-3 gap-8 items-start">
    <div class="md:col-span-2">
        <form method="POST" action="{{ route('checkout.process') }}" class="bg-white border border-stone-200/90 rounded-2xl p-6 sm:p-8 space-y-5 shadow-sm">
            @csrf

            <div>
                <label class="block text-xs font-bold text-stone-800 mb-1.5 uppercase tracking-wide">
                    Alamat Lengkap Pengiriman <span class="text-rose-500">*</span>
                </label>
                <textarea name="shipping_address" rows="3" required maxlength="500"
                          placeholder="Nama lengkap penerima, nomor telepon aktif, jalan, RT/RW, kelurahan, kecamatan, kota/kabupaten, kode pos"
                          class="w-full bg-stone-50 border border-stone-200 rounded-lg px-3.5 py-2.5 text-sm text-stone-900 font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('shipping_address', $defaultAddress) }}</textarea>
                <p class="text-[11px] text-stone-400 mt-1">Pastikan nomor kontak penerima dapat dihubungi kurir pengantar.</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-stone-800 mb-1.5 uppercase tracking-wide">
                    Catatan Pesanan <span class="text-stone-400 font-normal lowercase">(opsional)</span>
                </label>
                <textarea name="notes" rows="2" maxlength="500"
                          placeholder="Contoh: Titipkan di pos satpam atau jangan dibanting (makanan mudah remuk)"
                          class="w-full bg-stone-50 border border-stone-200 rounded-lg px-3.5 py-2.5 text-sm text-stone-900 font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('notes') }}</textarea>
            </div>

            <div class="bg-emerald-50/80 border border-emerald-200/80 rounded-xl p-4 text-xs text-emerald-900 flex items-start gap-3">
                <svg class="w-5 h-5 text-emerald-700 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div class="leading-relaxed">
                    <strong class="font-bold block mb-0.5">Metode Pembayaran Transfer Manual:</strong>
                    Setelah menekan tombol buat pesanan, nomor rekening bank dan instruksi unggah foto bukti transfer akan ditampilkan di halaman detail pesanan.
                </div>
            </div>

            <button type="submit"
                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3.5 rounded-lg text-sm transition shadow-sm">
                Konfirmasi & Buat Pesanan Sekarang &rarr;
            </button>
        </form>
    </div>

    <!-- Summary Sidebar -->
    <div>
        <div class="bg-white border border-stone-200/90 rounded-2xl p-6 shadow-sm sticky top-24">
            <h2 class="font-display text-sm font-bold text-stone-900 mb-4 pb-3 border-b border-stone-100 uppercase tracking-wide">
                Ringkasan Belanja
            </h2>
            <ul class="space-y-3.5 text-sm divide-y divide-stone-50">
                @foreach ($items as $item)
                    <li class="flex justify-between gap-3 pt-2 first:pt-0">
                        <div>
                            <p class="font-display text-xs font-bold text-stone-900">{{ $item['product']->name }}</p>
                            <p class="text-[11px] text-stone-500 mt-0.5">{{ $item['product']->store->name }} · {{ $item['qty'] }} unit</p>
                        </div>
                        <span class="font-semibold text-xs text-stone-800 whitespace-nowrap">
                            Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                        </span>
                    </li>
                @endforeach
            </ul>
            <div class="border-t border-stone-200 mt-4 pt-4 flex justify-between items-baseline">
                <span class="font-display text-xs font-bold text-stone-600 uppercase">Total Akhir</span>
                <span class="font-display text-lg font-black text-emerald-700">
                    Rp {{ number_format($total, 0, ',', '.') }}
                </span>
            </div>
        </div>
    </div>
</div>
@endsection