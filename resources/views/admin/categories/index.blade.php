@extends('layouts.app')

@section('title', 'Kelola Kategori')

@section('content')
<div class="flex items-center justify-between mb-4">
    <div>
        <h1 class="text-xl font-bold">Kelola Kategori</h1>
        <p class="text-sm text-gray-500">Kategori global yang dipakai seluruh merchant GANDENG.</p>
    </div>
    <a href="{{ route('admin.categories.create') }}"
       class="bg-emerald-600 text-white px-4 py-2 rounded text-sm hover:bg-emerald-700">
        + Tambah Kategori
    </a>
</div>

<div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left text-gray-600">
            <tr>
                <th class="px-4 py-3 font-medium">Nama</th>
                <th class="px-4 py-3 font-medium">Slug</th>
                <th class="px-4 py-3 font-medium">Ikon</th>
                <th class="px-4 py-3 font-medium">Jumlah Produk</th>
                <th class="px-4 py-3 font-medium text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($categories as $category)
                <tr>
                    <td class="px-4 py-3 font-medium">{{ $category->name }}</td>
                    <td class="px-4 py-3 text-gray-500"><code>{{ $category->slug }}</code></td>
                    <td class="px-4 py-3 text-gray-500">{{ $category->icon ?: '—' }}</td>
                    <td class="px-4 py-3">{{ $category->products_count }}</td>
                    <td class="px-4 py-3 text-right space-x-3">
                        <a href="{{ route('admin.categories.edit', $category) }}"
                           class="text-emerald-700 hover:underline">Edit</a>
                        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}"
                              class="inline" onsubmit="return confirm('Hapus kategori ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-400">Belum ada kategori.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $categories->links() }}</div>
@endsection