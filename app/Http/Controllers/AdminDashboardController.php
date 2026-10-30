<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'mitra_aktif' => Store::where('status', 'active')->count(),
            'mitra_pending' => Store::where('status', 'pending')->count(),
            'customer' => User::where('role', 'customer')->count(),
            'produk' => Product::where('is_active', true)->count(),
            'pesanan' => Order::count(),
            'transaksi' => OrderItem::whereHas('order', fn ($q) => $q->whereIn('status', ['paid', 'processing', 'shipped', 'completed']))->sum('subtotal'),
            'belum_diproses' => Order::where('status', 'paid')->count(),
        ];

        $tokoTerbaru = Store::with('user')->latest()->limit(5)->get();

        $pesananTerbaru = Order::with('user')
            ->withCount('items')
            ->latest()
            ->limit(5)
            ->get();

        $kategoriPopuler = Category::withCount(['products' => fn ($q) => $q->where('is_active', true)])
            ->orderByDesc('products_count')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'tokoTerbaru', 'pesananTerbaru', 'kategoriPopuler'));
    }
}