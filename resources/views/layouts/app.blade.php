<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'GANDENG') — Gerakan Anak Nusantara Dagang</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col">

<nav class="bg-white border-b border-gray-200">
    <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
        <a href="{{ route('home') }}" class="font-bold text-lg text-emerald-700">GANDENG</a>
        <div class="flex items-center gap-4 text-sm">
            @auth
                @if (auth()->user()->isCustomer())
                    <a href="{{ route('cart.index') }}" class="text-gray-700 hover:underline">Keranjang</a>
                    <a href="{{ route('orders.index') }}" class="text-gray-700 hover:underline">Pesanan</a>
                @elseif (auth()->user()->isMerchant())
                    <a href="{{ route('merchant.store.edit') }}" class="text-gray-700 hover:underline">Toko</a>
                    <a href="{{ route('merchant.products.index') }}" class="text-gray-700 hover:underline">Produk</a>
                    <a href="{{ route('merchant.dashboard') }}" class="text-gray-700 hover:underline">Dashboard</a>
                @elseif (auth()->user()->isAdmin())
                    <a href="{{ route('admin.categories.index') }}" class="text-gray-700 hover:underline">Kategori</a>
                    <a href="{{ route('admin.dashboard') }}" class="text-gray-700 hover:underline">Dashboard</a>
                @endif
                <span class="text-gray-400">|</span>
                <span class="text-gray-500">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="text-red-600 hover:underline">Keluar</button>
                </form>
            @else
                <a href="{{ route('catalog.index') }}" class="text-gray-700 hover:underline">Katalog</a>
                <a href="{{ route('login') }}" class="text-gray-700 hover:underline">Masuk</a>
                <a href="{{ route('register') }}" class="bg-emerald-600 text-white px-3 py-1.5 rounded hover:bg-emerald-700">Daftar</a>
            @endauth
        </div>
    </div>
</nav>

<main class="flex-1 max-w-6xl mx-auto w-full px-4 py-6">
    {{-- Flash message. `{{ }}` auto-escape -> anti-XSS. --}}
    @if (session('success'))
        <div class="mb-4 rounded bg-emerald-100 border border-emerald-300 text-emerald-800 px-4 py-3 text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded bg-red-100 border border-red-300 text-red-800 px-4 py-3 text-sm">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 rounded bg-red-100 border border-red-300 text-red-800 px-4 py-3 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</main>

<footer class="border-t border-gray-200 py-4 text-center text-xs text-gray-500">
    GANDENG — Gerakan Anak Nusantara Dagang · UAS Web Application Development
</footer>

</body>
</html>