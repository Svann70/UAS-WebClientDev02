<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MerchantOrderController extends Controller
{
    /**
     * Pesanan masuk HANYA yang berisi produk milik toko ini.
     *
     * Kenapa lewat order_items, bukan orders?
     * Satu order bisa berisi produk dari beberapa toko (multi-merchant checkout).
     * Kalau merchant melihat tabel `orders` langsung, dia akan melihat pesanan
     * toko lain beserta total belanja pembeli -> kebocoran data.
     */
    private function storeId(Request $request): int
    {
        $store = $request->user()->store()->first();
        abort_unless($store, 403, 'Lengkapi profil toko Anda terlebih dahulu.');

        return $store->id;
    }

    public function index(Request $request): View
    {
        $storeId = $this->storeId($request);

        $request->validate([
            'status' => ['nullable', 'in:pending,paid,processing,shipped,completed,cancelled'],
        ]);

        $items = OrderItem::query()
            ->where('store_id', $storeId)
            ->with(['order.user', 'product'])
            ->when($request->filled('status'), fn ($q) => $q->whereHas('order', fn ($o) => $o->where('status', $request->input('status'))))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        // Ringkasan pendapatan toko: hanya pesanan yang sudah dibayar ke atas.
        $omzet = OrderItem::where('store_id', $storeId)
            ->whereHas('order', fn ($q) => $q->whereIn('status', ['paid', 'processing', 'shipped', 'completed']))
            ->sum('subtotal');

        $menunggu = OrderItem::where('store_id', $storeId)
            ->whereHas('order', fn ($q) => $q->where('status', 'paid'))
            ->count();

        return view('merchant.orders.index', compact('items', 'omzet', 'menunggu'));
    }

    /**
     * Update status pesanan.
     *
     * Catatan penting: status ada di level ORDER, bukan per item. Kalau satu order
     * berisi produk dari dua toko, merchant A mengubah status berarti mengubah
     * status pesanan toko B juga. Untuk tugas ini alur itu diterima (pembayaran
     * terpusat di platform), tapi dibatasi: merchant hanya boleh mengubah pesanan
     * yang memang berisi produknya.
     */
    public function updateStatus(Request $request, OrderItem $item): RedirectResponse
    {
        $storeId = $this->storeId($request);

        // Anti-IDOR: item harus milik toko si merchant.
        abort_unless($item->store_id === $storeId, 403, 'Pesanan ini bukan untuk toko Anda.');

        $data = $request->validate([
            'status' => ['required', 'in:processing,shipped,completed,cancelled'],
        ]);

        $order = $item->order;

        // Transisi status dijaga: tidak boleh lompat mundur atau memproses yang belum dibayar.
        $boleh = match ($order->status) {
            'paid' => ['processing', 'cancelled'],
            'processing' => ['shipped', 'cancelled'],
            'shipped' => ['completed'],
            default => [],
        };

        if (! in_array($data['status'], $boleh, true)) {
            return back()->with('error', 'Status tidak bisa diubah dari "' . $order->status . '" ke "' . $data['status'] . '".');
        }

        $order->status = $data['status'];
        $order->save();

        return back()->with('success', 'Status pesanan ' . $order->order_code . ' diperbarui menjadi "' . $data['status'] . '".');
    }
}