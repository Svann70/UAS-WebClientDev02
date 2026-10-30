<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CustomerOrderController extends Controller
{
    public function index(Request $request): View
    {
        // Selalu lewat relasi user: tidak mungkin melihat order orang lain.
        $orders = $request->user()->orders()
            ->withCount('items')
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function show(Request $request, string $code): View
    {
        // Dicari berdasarkan order_code (unik & acak) DAN user_id milik sesi.
        // Dua syarat -> menebak kode saja tidak cukup untuk melihat pesanan orang lain.
        $order = $request->user()->orders()
            ->where('order_code', $code)
            ->with(['items.product.store'])
            ->firstOrFail();

        return view('orders.show', compact('order'));
    }

    public function uploadReceipt(Request $request, string $code): RedirectResponse
    {
        $order = $request->user()->orders()->where('order_code', $code)->firstOrFail();

        // Hanya pesanan yang masih menunggu pembayaran yang boleh diunggah buktinya.
        if ($order->status !== 'pending') {
            return back()->with('error', 'Bukti pembayaran hanya bisa diunggah saat status pesanan masih "Menunggu Pembayaran".');
        }

        $request->validate([
            'payment_receipt' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $old = $order->payment_receipt;
        $path = $request->file('payment_receipt')->store('receipts', 'public');

        if ($old && Storage::disk('public')->exists($old)) {
            Storage::disk('public')->delete($old);
        }

        // Status diubah lewat method terkontrol, bukan mass assignment.
        $order->payment_receipt = $path;
        $order->status = 'paid';
        $order->save();

        return back()->with('success', 'Bukti pembayaran terkirim. Menunggu verifikasi penjual.');
    }
}