@extends('layouts.app')

@section('title', 'Profil Toko')

@section('content')
<div class="max-w-2xl mx-auto bg-white border border-gray-200 rounded-lg p-6">
    <h1 class="text-xl font-bold mb-1">{{ $store ? 'Profil Toko' : 'Buat Profil Toko' }}</h1>
    <p class="text-sm text-gray-500 mb-4">
        @if ($store)
            Toko Anda sudah tampil di katalog publik. Slug: <code class="bg-gray-100 px-1 rounded">{{ $store->slug }}</code>
        @else
            Anda perlu membuat profil toko sebelum bisa menambahkan produk.
        @endif
    </p>

    {{-- Logo pratinjau --}}
    @if ($store?->logo)
        <img src="{{ asset('storage/' . $store->logo) }}" alt="Logo toko"
             class="w-20 h-20 object-cover rounded border border-gray-200 mb-4">
    @endif

    <form method="POST" action="{{ route('merchant.store.update') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium mb-1">Nama Toko</label>
            <input type="text" name="name" value="{{ old('name', $store?->name) }}" required maxlength="100"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Deskripsi Toko <span class="text-gray-400">(opsional)</span></label>
            <textarea name="description" rows="3" maxlength="1000"
                      class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('description', $store?->description) }}</textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Kota</label>
                <input type="text" name="city" value="{{ old('city', $store?->city) }}" required maxlength="100"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Logo Toko <span class="text-gray-400">(opsional)</span></label>
                <input type="file" name="logo" accept="image/jpeg,image/png,image/webp"
                       class="w-full text-sm border border-gray-300 rounded px-3 py-1.5 file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:bg-emerald-50 file:text-emerald-700">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Alamat Lengkap</label>
            <textarea name="address" rows="2" required maxlength="500"
                      class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('address', $store?->address) }}</textarea>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="bg-emerald-600 text-white px-4 py-2 rounded text-sm hover:bg-emerald-700">
                {{ $store ? 'Simpan Perubahan' : 'Buat Toko' }}
            </button>
        </div>
    </form>
</div>
@endsection