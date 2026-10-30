@extends('layouts.app')

@section('title', 'Kelola Mitra')

@section('content')
@php
    $badge = [
        'active' => 'bg-emerald-100 text-emerald-800',
        'pending' => 'bg-amber-100 text-amber-800',
        'suspended' => 'bg-red-100 text-red-800',
    ];
@endphp

<h1 class="text-xl font-bold mb-1">Kelola Mitra UMKM</h1>
<p class="text-sm text-gray-500 mb-4">Verifikasi toko baru dan tangguhkan toko yang melanggar.</p>

<form method="GET" class="flex flex-wrap gap-2 items-center mb-4 text-sm">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama toko / kota"
           class="border border-gray-300 rounded px-3 py-1.5 w-56">
    <select name="status" class="border border-gray-300 rounded px-3 py-1.5">
        <option value="">Semua status</option>
        <option value="pending" @selected(request('status') === 'pending')>Menunggu Verifikasi</option>
        <option value="active" @selected(request('status') === 'active')>Aktif</option>
        <option value="suspended" @selected(request('status') === 'suspended')>Ditangguhkan</option>
    </select>
    <button type="submit" class="bg-emerald-600 text-white px-3 py-1.5 rounded hover:bg-emerald-700">Filter</button>
    @if (request()->hasAny(['q', 'status']))
        <a href="{{ route('admin.stores.index') }}" class="text-gray-500 hover:underline">Reset</a>
    @endif
</form>

@if ($stores->isEmpty())
    <div class="bg-white border border-gray-200 rounded-lg py-12 text-center text-gray-400 text-sm">
        Tidak ada toko yang cocok.
    </div>
@else
    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-600">
                <tr>
                    <th class="px-4 py-3 font-medium">Toko</th>
                    <th class="px-4 py-3 font-medium">Pemilik</th>
                    <th class="px-4 py-3 font-medium">Kota</th>
                    <th class="px-4 py-3 font-medium">Produk</th>
                    <th class="px-4 py-3 font-medium">Status</th>
                    <th class="px-4 py-3 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($stores as $store)
                    <tr>
                        <td class="px-4 py-3">
                            <a href="{{ route('catalog.store', $store->slug) }}" target="_blank"
                               class="font-medium hover:underline">{{ $store->name }}</a>
                            <p class="text-xs text-gray-400">{{ $store->created_at->format('d M Y') }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <p>{{ $store->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $store->user->email }}</p>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $store->city }}</td>
                        <td class="px-4 py-3">{{ $store->products_count }}</td>
                        <td class="px-4 py-3">
                            <span class="text-xs px-2 py-0.5 rounded {{ $badge[$store->status] ?? 'bg-gray-100' }}">
                                {{ ucfirst($store->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <form method="POST" action="{{ route('admin.stores.status', $store) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                @if ($store->status !== 'active')
                                    <input type="hidden" name="status" value="active">
                                    <button type="submit" class="text-emerald-700 text-xs hover:underline">Aktifkan</button>
                                @else
                                    <input type="hidden" name="status" value="suspended">
                                    <button type="submit" class="text-red-600 text-xs hover:underline"
                                            onclick="return confirm('Tangguhkan toko ini? Produknya akan disembunyikan dari katalog.')">
                                        Tangguhkan
                                    </button>
                                @endif
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $stores->links() }}</div>
@endif
@endsection