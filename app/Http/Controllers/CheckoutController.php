<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    /** Ambil isi keranjang + produk yang masih valid, dipakai index() dan process(). */
    private function cartItems(): array
    {
        $cart = session()->get('cart', []);

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
                continue;
            }
            $product = $products[$id];
            $subtotal = (float) $product->price * $qty;
            $total += $subtotal;
            $items[] = ['product' => $product, 'qty' => $qty, 'subtotal' => $subtotal];
        }

        return [$items, $total];
    }

    public function index(Request $request): View|RedirectResponse
    {
        [$items, $total] = $this->cartItems();

        if (empty($items)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang Anda kosong.');
        }

        return view('checkout.index', [
            'items' => $items,
            'total' => $total,
            'defaultAddress' => '',
        ]);
    }

    public function process(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'shipping_address' => ['required', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        [$items, $total] = $this->cartItems();

        if (empty($items)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang Anda kosong.');
        }

        /**
         * Semua penulisan dibungkus transaksi database.
         * Kalau satu langkah gagal (mis. stok tiba-tiba habis), semuanya di-rollback:
         * tidak ada order setengah jadi dan stok tidak berkurang percuma.
         */
        $order = DB::transaction(function () use ($request, $data, $items, $total) {
            $order = Order::create([
                'user_id' => $request->user()->id,
                'total_amount' => $total,
                'shipping_address' => $data['shipping_address'],
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($items as $item) {
                $product = $item['product'];
                $qty = $item['qty'];

                // Lock baris produk: dua pembeli terakhir tidak bisa sama-sama mengambil stok yang sama.
                $fresh = Product::whereKey($product->id)->lockForUpdate()->first();

                if (! $fresh || $fresh->stock < $qty) {
                    throw new \RuntimeException('Stok "' . $product->name . '" tidak mencukupi.');
                }

                $order->items()->create([
                    'product_id' => $fresh->id,
                    'store_id' => $fresh->store_id,
                    'quantity' => $qty,
                    // Harga disalin dari database saat transaksi, BUKAN dari session/form.
                    'unit_price' => $fresh->price,
                    'subtotal' => (float) $fresh->price * $qty,
                ]);

                $fresh->decrement('stock', $qty);
            }

            return $order;
        });

        session()->forget('cart');

        return redirect()->route('orders.show', $order->order_code)
            ->with('success', 'Pesanan ' . $order->order_code . ' berhasil dibuat. Silakan lakukan pembayaran.');
    }
}