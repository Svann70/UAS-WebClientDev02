<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        // Semua query difilter is_active + toko aktif: produk "disembunyikan" merchant
        // dan produk dari toko yang disuspend admin tidak boleh muncul di katalog publik.
        $products = Product::query()
            ->where('is_active', true)
            ->whereHas('store', fn ($q) => $q->where('status', 'active'))
            ->with(['category', 'store'])
            ->latest()
            ->limit(8)
            ->get();

        $categories = Category::whereHas('products', fn ($q) => $q->where('is_active', true))
            ->withCount(['products' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('name')
            ->get();

        return view('home', compact('products', 'categories'));
    }
}