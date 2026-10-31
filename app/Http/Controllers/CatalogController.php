<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(Request $request): View
    {
        // Validasi parameter query: nilai aneh dari URL tidak boleh masuk ke query.
        // Tanpa ini, `?category[]=1` bisa memicu error 500 alih-alih diabaikan.
        $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:60'],
            'sort' => ['nullable', 'in:terbaru,termurah,termahal'],
        ]);

        $products = Product::query()
            ->where('is_active', true)
            ->whereHas('store', fn ($q) => $q->where('status', 'active'))
            ->with(['category', 'store'])
            // Pencarian pakai parameter binding (Eloquent), bukan string concat -> anti SQLi.
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%' . $request->input('q') . '%';
                $q->where(fn ($sub) => $sub->where('name', 'like', $term)
                    ->orWhere('description', 'like', $term));
            })
            ->when($request->filled('category'), function ($q) use ($request) {
                $q->whereHas('category', fn ($c) => $c->where('slug', $request->input('category')));
            })
            ->when(true, function ($q) use ($request) {
                match ($request->input('sort')) {
                    'termurah' => $q->orderBy('price'),
                    'termahal' => $q->orderByDesc('price'),
                    default => $q->latest(),
                };
            })
            ->paginate(12)
            ->withQueryString(); // filter tetap menempel saat pindah halaman

        $categories = Category::withCount(['products' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('name')
            ->get();

        return view('catalog.index', compact('products', 'categories'));
    }

    public function show(string $slug): View
    {
        // Route model binding manual lewat slug: URL memakai slug, bukan ID -> anti enumerasi.
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->whereHas('store', fn ($q) => $q->where('status', 'active'))
            ->with(['category', 'store'])
            ->firstOrFail();

        // Produk lain dari toko yang sama -> rekomendasi silang, satu query.
        $lainnya = Product::where('store_id', $product->store_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->limit(4)
            ->get();

        return view('catalog.show', compact('product', 'lainnya'));
    }

    public function store(string $slug): View
    {
        $store = \App\Models\Store::where('slug', $slug)
            ->where('status', 'active')
            ->with('user')
            ->firstOrFail();

        $products = $store->products()
            ->where('is_active', true)
            ->with('category')
            ->latest()
            ->paginate(12);

        return view('catalog.store', compact('store', 'products'));
    }
}