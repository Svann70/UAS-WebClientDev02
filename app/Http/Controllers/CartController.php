<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * Keranjang disimpan di session, bukan database.
     * Alasan: keranjang adalah data sementara milik satu browser. Menyimpannya
     * di DB menambah tabel + query untuk data yang belum tentu jadi transaksi.
     *
     * Format: ['product_id' => quantity]
     */
    private const KEY = 'cart';

    private function raw(): array
    {
        return session()->get(self::KEY, []);
    }

    public function index(): View
    {
        $cart = $this->raw();

        // Hanya produk aktif dari toko aktif yang boleh tampil.
        $products = Product::whereIn('id', array_keys($cart))
            ->where('is_active', true)
            ->whereHas('store', fn ($q) => $q->where('status', 'active'))
            ->with('store')
            ->get()
            ->keyBy('id');

        $items = [];
        $total = 0;

        foreach ($cart as $id => $qty) {
            if (! isset($products[$id])) {
                continue; // produk sudah dihapus/dinonaktifkan -> lewati
            }
            $product = $products[$id];
            $subtotal = (float) $product->price * $qty;
            $total += $subtotal;
            $items[] = ['product' => $product, 'qty' => $qty, 'subtotal' => $subtotal];
        }

        return view('cart.index', compact('items', 'total'));
    }

    public function add(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        // Validasi ulang di server: jangan percaya max= dari atribut HTML.
        abort_if(! $product->is_active, 404);
        abort_if($product->stock < 1, 400, 'Stok produk habis.');

        $cart = $this->raw();
        $newQty = ($cart[$product->id] ?? 0) + $data['quantity'];

        // Batasi ke stok yang benar-benar ada.
        if ($newQty > $product->stock) {
            $newQty = $product->stock;
            session()->flash('error', 'Jumlah disesuaikan dengan stok tersedia (' . $product->stock . ').');
        }

        $cart[$product->id] = $newQty;
        session()->put(self::KEY, $cart);

        return back()->with('success', '"' . $product->name . '" ditambahkan ke keranjang.');
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $cart = $this->raw();

        if ($data['quantity'] === 0) {
            unset($cart[$product->id]);
        } else {
            $cart[$product->id] = min($data['quantity'], max(1, $product->stock));
        }

        session()->put(self::KEY, $cart);

        return back()->with('success', 'Keranjang diperbarui.');
    }

    public function remove(Product $product): RedirectResponse
    {
        $cart = $this->raw();
        unset($cart[$product->id]);
        session()->put(self::KEY, $cart);

        return back()->with('success', 'Item dihapus dari keranjang.');
    }
}