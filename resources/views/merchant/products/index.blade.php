@extends('layouts.app')

@section('title', 'Produk Saya')

@section('content')
<div class="flex items-center justify-between mb-4">
    <div>
        <h1 class="text-xl font-bold">Produk Saya</h1>
        <p class="text-sm text-gray-500">Kelola katalog produk toko Anda.</p>
    </div>
    <a href="{{ route('merchant.products.create') }}"
       class="bg-emerald-600 text-white px-4 py-2 rounded text-sm hover:bg-emerald-700">
        + Tambah Produk
    </a>
</div>

<div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left text-gray-600">
            <tr>
                <th class="px-4 py-3 font-medium">Foto</th>
                <th class="px-4 py-3 font-medium">Nama</th>
                <th class="px-4 py-3 font-medium">Kategori</th>
                <th class="px-4 py-3 font-medium">Harga</th>
                <th class="px-4 py-3 font-medium">Stok</th>
                <th class="px-4 py-3 font-medium">Status</th>
                <th class="px-4 py-3 font-medium text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($products as $product)
                <tr>
                    <td class="px-4 py-3">
                        @if ($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                                 class="w-12 h-12 object-cover rounded border border-gray-200">
                        @else
                            <div class="w-12 h-12 rounded bg-gray-100 flex items-center justify-center text-gray-400 text-xs">—</div>
                        @endif
                    </td>
                    <td class="px-4 py-3 font-medium">{{ $product->name }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $product->category->name }}</td>
                    <td class="px-4 py-3">Rp {{ number_format((float) $product->price, 0, ',', '.') }}</td>
                    <td class="px-4 py-3">{{ $product->stock }}</td>
                    <td class="px-4 py-3">
                        @if ($product->is_active)
                            <span class="text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded text-xs">Aktif</span>
                        @else
                            <span class="text-gray-600 bg-gray-100 px-2 py-0.5 rounded text-xs">Nonaktif</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right space-x-3">
                        <a href="{{ route('merchant.products.edit', $product) }}"
                           class="text-emerald-700 hover:underline">Edit</a>
                        <form method="POST" action="{{ route('merchant.products.destroy', $product) }}"
                              class="inline" onsubmit="return confirm('Hapus produk ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-400">
                        Belum ada produk. Mulai dengan menambahkan produk pertama Anda.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $products->links() }}</div>
@endsection