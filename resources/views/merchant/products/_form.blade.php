@php
    // Partial dipakai create & edit. Satu sumber field.
    $product = $product ?? null;
@endphp

<div>
    <label class="block text-sm font-medium mb-1">Nama Produk</label>
    <input type="text" name="name" value="{{ old('name', $product?->name) }}" required maxlength="150"
           class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
</div>

<div>
    <label class="block text-sm font-medium mb-1">Kategori</label>
    <select name="category_id" required
            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
        <option value="">— Pilih kategori —</option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }}" @selected(old('category_id', $product?->category_id) == $category->id)>
                {{ $category->name }}
            </option>
        @endforeach
    </select>
</div>

<div>
    <label class="block text-sm font-medium mb-1">Deskripsi</label>
    <textarea name="description" required maxlength="2000" rows="4"
              class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('description', $product?->description) }}</textarea>
</div>

<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium mb-1">Harga (Rp)</label>
        <input type="number" name="price" value="{{ old('price', $product?->price) }}" required min="0" step="1"
               class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Stok</label>
        <input type="number" name="stock" value="{{ old('stock', $product?->stock ?? 0) }}" required min="0" step="1"
               class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
    </div>
</div>

<div>
    <label class="block text-sm font-medium mb-1">Foto Produk <span class="text-gray-400">(opsional)</span></label>
    @if ($product?->image)
        <img src="{{ asset('storage/' . $product->image) }}" alt="Foto saat ini"
             class="w-24 h-24 object-cover rounded border border-gray-200 mb-2">
        <p class="text-xs text-gray-400 mb-2">Biarkan kosong bila tidak ingin mengganti foto.</p>
    @endif
    <input type="file" name="image" accept="image/jpeg,image/png,image/webp"
           class="w-full text-sm border border-gray-300 rounded px-3 py-2 file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:bg-emerald-50 file:text-emerald-700">
    <p class="text-xs text-gray-400 mt-1">Format JPG, PNG, atau WEBP. Maksimal 2 MB.</p>
</div>

<label class="flex items-center gap-2 text-sm text-gray-600">
    <input type="checkbox" name="is_active" value="1"
           @checked(old('is_active', $product?->is_active ?? true))
           class="rounded border-gray-300">
    Tampilkan produk di katalog publik
</label>

<div class="flex gap-3 pt-2">
    <button type="submit" class="bg-emerald-600 text-white px-4 py-2 rounded text-sm hover:bg-emerald-700">
        Simpan
    </button>
    <a href="{{ route('merchant.products.index') }}"
       class="px-4 py-2 rounded text-sm border border-gray-300 hover:bg-gray-50">Batal</a>
</div>