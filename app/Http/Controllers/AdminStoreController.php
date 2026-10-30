<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminStoreController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate([
            'status' => ['nullable', 'in:pending,active,suspended'],
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        $stores = Store::query()
            ->with('user')
            ->withCount('products')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%' . $request->input('q') . '%';
                $q->where(fn ($sub) => $sub->where('name', 'like', $term)
                    ->orWhere('city', 'like', $term));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.stores.index', compact('stores'));
    }

    /**
     * Ubah status toko (verifikasi / suspend / aktifkan kembali).
     *
     * Kenapa method khusus, bukan mass assignment?
     * `status` sengaja TIDAK ada di $fillable Store. Hanya jalur ini yang bisa
     * mengubahnya, dan hanya admin yang boleh masuk (middleware role:admin).
     */
    public function updateStatus(Request $request, Store $store): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,active,suspended'],
        ]);

        if ($store->status === $data['status']) {
            return back()->with('error', 'Status toko sudah "' . $data['status'] . '".');
        }

        $store->status = $data['status'];
        $store->save();

        $pesan = match ($data['status']) {
            'active' => 'Toko "' . $store->name . '" berhasil diverifikasi dan aktif di katalog.',
            'suspended' => 'Toko "' . $store->name . '" ditangguhkan. Produknya disembunyikan dari katalog.',
            'pending' => 'Toko "' . $store->name . '" dikembalikan ke status menunggu verifikasi.',
        };

        return back()->with('success', $pesan);
    }
}