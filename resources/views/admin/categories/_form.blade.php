@php
    // Partial dipakai oleh create & edit.
    // Satu sumber field -> tidak mungkin form create dan edit berbeda.
    $category = $category ?? null;
@endphp

<div>
    <label class="block text-sm font-medium mb-1">Nama Kategori</label>
    <input type="text" name="name" value="{{ old('name', $category?->name) }}" required maxlength="50"
           class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
    <p class="text-xs text-gray-400 mt-1">Slug dibuat otomatis dari nama.</p>
</div>

<div>
    <label class="block text-sm font-medium mb-1">Ikon <span class="text-gray-400">(opsional)</span></label>
    <input type="text" name="icon" value="{{ old('icon', $category?->icon) }}" maxlength="100"
           placeholder="mis. bi-basket"
           class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
</div>

<div class="flex gap-3">
    <button type="submit" class="bg-emerald-600 text-white px-4 py-2 rounded text-sm hover:bg-emerald-700">
        Simpan
    </button>
    <a href="{{ route('admin.categories.index') }}"
       class="px-4 py-2 rounded text-sm border border-gray-300 hover:bg-gray-50">Batal</a>
</div>