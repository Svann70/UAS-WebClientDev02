<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'GANDENG') — Gerakan Anak Nusantara Dagang</title>
    
    <!-- Google Fonts: Plus Jakarta Sans (Headings) & Inter (Body) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                        display: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d',
                        },
                        amberbrand: {
                            500: '#f59e0b',
                            600: '#d97706',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-feature-settings: "cv02", "cv03", "cv04", "cv11"; }
    </style>
</head>
<body class="bg-[#FAF9F5] text-stone-900 min-h-screen flex flex-col antialiased selection:bg-brand-100 selection:text-brand-900">

<!-- Main Navigation -->
<nav class="bg-white/95 backdrop-blur-md sticky top-0 z-50 border-b border-stone-200/80">
    <div class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between gap-4">
        <!-- Logo -->
        <a href="{{ route('home') }}" class="flex items-center gap-2 group">
            <div class="w-8 h-8 rounded-lg bg-brand-700 text-white flex items-center justify-center font-display font-extrabold text-lg shadow-sm group-hover:bg-brand-800 transition">
                G
            </div>
            <div class="flex flex-col">
                <span class="font-display font-extrabold text-lg text-stone-900 tracking-tight leading-none">GANDENG</span>
                <span class="text-[10px] text-stone-500 font-medium tracking-wider uppercase mt-0.5">Pasar UMKM</span>
            </div>
        </a>

        <!-- Quick Links -->
        <div class="flex items-center gap-1 sm:gap-4 text-sm font-medium">
            <a href="{{ route('catalog.index') }}" class="px-3 py-1.5 rounded-md text-stone-600 hover:text-stone-900 hover:bg-stone-100 transition">
                Katalog
            </a>

            @auth
                @if (auth()->user()->isCustomer())
                    <a href="{{ route('cart.index') }}" class="px-3 py-1.5 rounded-md text-stone-600 hover:text-stone-900 hover:bg-stone-100 transition flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        <span>Keranjang</span>
                    </a>
                    <a href="{{ route('orders.index') }}" class="px-3 py-1.5 rounded-md text-stone-600 hover:text-stone-900 hover:bg-stone-100 transition">
                        Pesanan
                    </a>
                @elseif (auth()->user()->isMerchant())
                    <a href="{{ route('merchant.dashboard') }}" class="px-3 py-1.5 rounded-md text-stone-600 hover:text-stone-900 hover:bg-stone-100 transition">
                        Dashboard
                    </a>
                    <a href="{{ route('merchant.products.index') }}" class="px-3 py-1.5 rounded-md text-stone-600 hover:text-stone-900 hover:bg-stone-100 transition">
                        Produk Toko
                    </a>
                    <a href="{{ route('merchant.store.edit') }}" class="px-3 py-1.5 rounded-md text-stone-600 hover:text-stone-900 hover:bg-stone-100 transition">
                        Profil Toko
                    </a>
                @elseif (auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="px-3 py-1.5 rounded-md text-stone-600 hover:text-stone-900 hover:bg-stone-100 transition">
                        Dashboard
                    </a>
                    <a href="{{ route('admin.stores.index') }}" class="px-3 py-1.5 rounded-md text-stone-600 hover:text-stone-900 hover:bg-stone-100 transition">
                        Mitra
                    </a>
                    <a href="{{ route('admin.categories.index') }}" class="px-3 py-1.5 rounded-md text-stone-600 hover:text-stone-900 hover:bg-stone-100 transition">
                        Kategori
                    </a>
                @endif

                <div class="h-4 w-px bg-stone-300 mx-1 hidden sm:block"></div>

                <div class="flex items-center gap-2">
                    <span class="text-stone-500 text-xs hidden md:inline truncate max-w-[120px]">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 text-xs font-semibold rounded-md text-stone-600 hover:text-red-700 hover:bg-red-50 transition border border-stone-200">
                            Keluar
                        </button>
                    </form>
                </div>
            @else
                <div class="h-4 w-px bg-stone-200 mx-1 hidden sm:block"></div>
                <a href="{{ route('login') }}" class="px-3 py-1.5 text-stone-700 hover:text-stone-900 font-semibold transition">
                    Masuk
                </a>
                <a href="{{ route('register') }}" class="bg-brand-700 hover:bg-brand-800 text-white px-4 py-1.5 rounded-md font-semibold text-xs transition shadow-sm">
                    Daftar Akun
                </a>
            @endauth
        </div>
    </div>
</nav>

<!-- Content Body -->
<main class="flex-1 max-w-6xl mx-auto w-full px-4 py-8">
    {{-- Feedback Alerts --}}
    @if (session('success'))
        <div class="mb-6 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm flex items-start gap-3 shadow-sm">
            <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <div class="font-medium">{{ session('success') }}</div>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 text-sm flex items-start gap-3 shadow-sm">
            <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div class="font-medium">{{ session('error') }}</div>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 text-sm shadow-sm">
            <div class="font-semibold mb-1">Periksa kembali data Anda:</div>
            <ul class="list-disc list-inside space-y-0.5 text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</main>

<!-- Footer -->
<footer class="bg-white border-t border-stone-200 mt-16 py-8 text-stone-500 text-xs">
    <div class="max-w-6xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <span class="font-display font-bold text-stone-800 text-sm">GANDENG</span>
            <span class="text-stone-400 ml-2">Gerakan Anak Nusantara Dagang</span>
        </div>
        <p class="text-stone-400 text-center sm:text-right">
            Proyek UAS Web Application Development &copy; {{ date('Y') }}
        </p>
    </div>
</footer>

</body>
</html>