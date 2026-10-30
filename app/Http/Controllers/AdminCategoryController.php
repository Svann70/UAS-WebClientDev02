<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminCategoryController extends Controller
{
    /**
     * Aturan validasi dipakai bersama oleh store() dan update().
     * Ditaruh di satu method supaya tidak ada dua definisi yang bisa berbeda
     * (sumber bug klasik: store divalidasi ketat, update lupa).
     */
    private function rules(?Category $category = null): array
    {
        return [
            'name' => ['required', 'string', 'max:50', 'unique:categories,name' . ($category ? ",{$category->id}" : '')],
            'icon' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function index(): View
    {
        // withCount('products') -> satu query agregat, bukan N+1 query per kategori.
        $categories = Category::withCount('products')->orderBy('name')->paginate(10);

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules());

        // Slug dibuat server-side dari nama + suffix acak bila bentrok.
        // Jangan terima slug dari form: user bisa mengirim slug aneh/duplikat.
        $data['slug'] = $this->uniqueSlug($data['name']);

        Category::create($data);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori "' . $data['name'] . '" berhasil ditambahkan.');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $request->validate($this->rules($category));

        // Slug ikut diperbarui hanya kalau nama berubah, agar URL lama tetap valid.
        if ($data['name'] !== $category->name) {
            $data['slug'] = $this->uniqueSlug($data['name'], $category->id);
        }

        $category->update($data);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori "' . $category->name . '" berhasil diperbarui.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        // Cek manual: kalau kategori masih dipakai produk, tolak penghapusan.
        // FK di database pakai `restrict`, jadi tanpa cek ini user dapat error 500.
        if ($category->products()->exists()) {
            return back()->with('error', 'Kategori tidak bisa dihapus karena masih dipakai oleh ' . $category->products()->count() . ' produk.');
        }

        $name = $category->name;
        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori "' . $name . '" berhasil dihapus.');
    }

    /** Buat slug unik: "keripik" -> "keripik-2" -> "keripik-3" bila sudah dipakai. */
    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'kategori';
        $slug = $base;
        $i = 1;

        while (Category::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base . '-' . (++$i);
        }

        return $slug;
    }
}