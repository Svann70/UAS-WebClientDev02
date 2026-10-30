<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MerchantStoreController extends Controller
{
    /**
     * Satu form untuk dua keadaan: belum punya toko (create) dan sudah punya (edit).
     * Dipisah method-nya supaya route & alurnya jelas untuk presentasi.
     */
    public function edit(Request $request): View
    {
        $store = $request->user()->store()->first();

        return view('merchant.store.edit', compact('store'));
    }

    public function update(Request $request): RedirectResponse
    {
        $store = $request->user()->store()->first();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'city' => ['required', 'string', 'max:100'],
            'address' => ['required', 'string', 'max:500'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('logo')) {
            $old = $store?->logo;
            $data['logo'] = $request->file('logo')->store('stores', 'public');

            if ($old && Storage::disk('public')->exists($old)) {
                Storage::disk('public')->delete($old);
            }
        }

        if (! $store) {
            // Toko baru: user_id & slug ditentukan server, status tetap default 'active'.
            $data['user_id'] = $request->user()->id;
            $data['slug'] = $this->uniqueSlug($data['name']);
            $request->user()->store()->create($data);

            return redirect()->route('merchant.store.edit')
                ->with('success', 'Profil toko berhasil dibuat. Sekarang Anda bisa menambah produk.');
        }

        // Nama berubah -> slug ikut diperbarui agar mencerminkan nama toko terkini.
        if ($data['name'] !== $store->name) {
            $data['slug'] = $this->uniqueSlug($data['name'], $store->id);
        }

        $store->update($data);

        return redirect()->route('merchant.store.edit')
            ->with('success', 'Profil toko berhasil diperbarui.');
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'toko';
        $slug = $base;
        $i = 1;

        while (\App\Models\Store::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base . '-' . (++$i);
        }

        return $slug;
    }
}