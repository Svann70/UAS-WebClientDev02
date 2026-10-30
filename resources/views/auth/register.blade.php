@extends('layouts.app')

@section('title', 'Daftar Akun')

@section('content')
<div class="max-w-md mx-auto bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <h1 class="text-xl font-bold mb-1">Daftar Akun</h1>
    <p class="text-sm text-gray-500 mb-5">Bergabung sebagai pembeli atau daftarkan UMKM Anda.</p>

    <form method="POST" action="{{ route('register.store') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium mb-1">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name') }}" required maxlength="100"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required maxlength="100"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Nomor HP <span class="text-gray-400">(opsional)</span></label>
            <input type="text" name="phone" value="{{ old('phone') }}" maxlength="20"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Daftar Sebagai</label>
            <select name="role" required
                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <option value="customer" @selected(old('role') === 'customer')>Pembeli (Customer)</option>
                <option value="merchant" @selected(old('role') === 'merchant')>Pemilik UMKM (Merchant)</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Password</label>
            <input type="password" name="password" required minlength="8"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <p class="text-xs text-gray-400 mt-1">Minimal 8 karakter.</p>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Ulangi Password</label>
            <input type="password" name="password_confirmation" required minlength="8"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
        </div>

        <button type="submit"
                class="w-full bg-emerald-600 text-white py-2 rounded text-sm font-medium hover:bg-emerald-700">
            Daftar
        </button>
    </form>

    <p class="text-sm text-center text-gray-500 mt-4">
        Sudah punya akun? <a href="{{ route('login') }}" class="text-emerald-700 hover:underline">Masuk</a>
    </p>
</div>
@endsection