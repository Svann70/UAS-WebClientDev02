<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MerchantProductController extends Controller
{
    /**
     * Toko milik merchant yang sedang login.
     * Kalau belum punya toko -> diarahkan membuat toko dulu.
     * Semua query produk WAJIB lewat store ini supaya merchant tidak bisa
     * menyentuh produk toko lain (anti-IDOR).
     */
    private function currentStore(Request $request)
    {
        $store = $request->user()->store()->first();

        abort_unless($store, 403, 'Lengkapi profil toko Anda terlebih dahulu.');

        return $store;
    }

    private function rules(): array
    {
        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string', 'max:2000'],
            'price' => ['required', 'numeric', 'min:0', 'max:9999999999'],
            'stock' => ['required', 'integer', 'min:0', 'max:1000000'],
            // mimes memeriksa isi file (magic bytes), bukan cuma ekstensi -> anti RCE upload.
            // max:2048 = 2 MB dalam kilobyte.
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function index(Request $request): View
    {
        $store = $this->currentStore($request);

        // Produk difilter dari store_id, bukan dari parameter URL.
        $products = $store->products()
            ->with('category')
            ->latest()
            ->paginate(10);

        return view('merchant.products.index', compact('products'));
    }

    public function create(Request $request): View
    {
        $this->currentStore($request);
        $categories = Category::orderBy('name')->get();

        return view('merchant.products.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $store = $this->currentStore($request);
        $data = $request->validate($this->rules());

        $data['store_id'] = $store->id;      // dipaksa dari sesi, bukan dari form
        $data['slug'] = $this->uniqueSlug($data['name']);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            // store() menghasilkan nama file acak (hash) -> nama asli user tidak pernah dipakai.
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($data);

        return redirect()->route('merchant.products.index')
            ->with('success', 'Produk "' . $data['name'] . '" berhasil ditambahkan.');
    }

    public function edit(Request $request, Product $product): View
    {
        $this->authorizeProduct($request, $product);
        $categories = Category::orderBy('name')->get();

        return view('merchant.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $this->authorizeProduct($request, $product);
        $data = $request->validate($this->rules());

        // store_id sengaja tidak ikut diupdate: produk tidak boleh pindah toko.
        if ($data['name'] !== $product->name) {
            $data['slug'] = $this->uniqueSlug($data['name'], $product->id);
        }

        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $old = $product->image;
            $data['image'] = $request->file('image')->store('products', 'public');

            // Hapus file lama SETELAH upload baru berhasil, supaya tidak ada momen gambar hilang.
            if ($old && Storage::disk('public')->exists($old)) {
                Storage::disk('public')->delete($old);
            }
        }

        $product->update($data);

        return redirect()->route('merchant.products.index')
            ->with('success', 'Produk "' . $product->name . '" berhasil diperbarui.');
    }

    public function destroy(Request $request, Product $product): RedirectResponse
    {
        $this->authorizeProduct($request, $product);

        // Produk yang sudah pernah dibeli tidak boleh dihapus: riwayat transaksi harus utuh.
        if ($product->orderItems()->exists()) {
            return back()->with('error', 'Produk tidak bisa dihapus karena sudah ada di riwayat pesanan. Nonaktifkan saja produknya.');
        }

        $name = $product->name;

        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('merchant.products.index')
            ->with('success', 'Produk "' . $name . '" berhasil dihapus.');
    }

    /** Pastikan produk milik toko si merchant. Selain itu -> 403. */
    private function authorizeProduct(Request $request, Product $product): void
    {
        abort_unless($product->store_id === $this->currentStore($request)->id, 403, 'Produk ini bukan milik toko Anda.');
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'produk';
        $slug = $base;
        $i = 1;

        while (Product::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base . '-' . (++$i);
        }

        return $slug;
    }
}