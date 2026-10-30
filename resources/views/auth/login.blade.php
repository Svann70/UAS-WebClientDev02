@extends('layouts.app')

@section('title', 'Masuk')

@section('content')
<div class="max-w-md mx-auto bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <h1 class="text-xl font-bold mb-1">Masuk</h1>
    <p class="text-sm text-gray-500 mb-5">Gunakan akun GANDENG Anda.</p>

    <form method="POST" action="{{ route('login.attempt') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Password</label>
            <input type="password" name="password" required
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
        </div>

        <label class="flex items-center gap-2 text-sm text-gray-600">
            <input type="checkbox" name="remember" value="1" class="rounded border-gray-300">
            Ingat saya
        </label>

        <button type="submit"
                class="w-full bg-emerald-600 text-white py-2 rounded text-sm font-medium hover:bg-emerald-700">
            Masuk
        </button>
    </form>

    <p class="text-sm text-center text-gray-500 mt-4">
        Belum punya akun? <a href="{{ route('register') }}" class="text-emerald-700 hover:underline">Daftar</a>
    </p>
</div>
@endsection