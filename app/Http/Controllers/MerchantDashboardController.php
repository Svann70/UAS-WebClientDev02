<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MerchantDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $store = $request->user()->store()->first();

        abort_unless($store, 403, 'Lengkapi profil toko Anda terlebih dahulu.');

        $products = $store->products();

        // Ringkasan satu query agregat per baris, bukan loop query.
        $stats = [
            'total_produk' => (clone $products)->count(),
            'produk_aktif' => (clone $products)->where('is_active', true)->count(),
            'stok_menipis' => (clone $products)->where('is_active', true)->where('stock', '<=', 5)->count(),
            'pesanan_masuk' => $store->orderItems()->whereHas('order', fn ($q) => $q->where('status', 'paid'))->count(),
        ];

        $omzet = $store->orderItems()
            ->whereHas('order', fn ($q) => $q->whereIn('status', ['paid', 'processing', 'shipped', 'completed']))
            ->sum('subtotal');

        // Produk yang perlu perhatian: stok habis/menipis, urut paling kritis.
        $perhatian = (clone $products)
            ->where('is_active', true)
            ->where('stock', '<=', 5)
            ->orderBy('stock')
            ->limit(5)
            ->get();

        $terbaru = $store->orderItems()
            ->with(['order.user', 'product'])
            ->latest()
            ->limit(5)
            ->get();

        return view('merchant.dashboard', compact('store', 'stats', 'omzet', 'perhatian', 'terbaru'));
    }
}