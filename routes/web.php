<?php

use App\Http\Controllers\AdminCategoryController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\AdminStoreController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MerchantDashboardController;
use App\Http\Controllers\MerchantOrderController;
use App\Http\Controllers\MerchantProductController;
use App\Http\Controllers\MerchantStoreController;
use Illuminate\Support\Facades\Route;

// ============ PUBLIK ============
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/produk', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/produk/{slug}', [CatalogController::class, 'show'])->name('catalog.show');
Route::get('/toko/{slug}', [CatalogController::class, 'store'])->name('catalog.store');

// ============ AUTENTIKASI (hanya untuk tamu) ============
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});

// Logout wajib POST + CSRF: GET logout bisa dieksploitasi lewat <img src> untuk memaksa user keluar.
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// ============ CUSTOMER (keranjang) ============
Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');
    Route::post('/keranjang/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/keranjang/{product}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/keranjang/{product}', [CartController::class, 'remove'])->name('cart.remove');

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');

    Route::get('/pesanan', [CustomerOrderController::class, 'index'])->name('orders.index');
    Route::get('/pesanan/{code}', [CustomerOrderController::class, 'show'])->name('orders.show');
    Route::post('/pesanan/{code}/bukti', [CustomerOrderController::class, 'uploadReceipt'])->name('orders.receipt');
});

// ============ MERCHANT ============
Route::middleware(['auth', 'role:merchant'])->prefix('merchant')->name('merchant.')->group(function () {
    Route::get('/dashboard', [MerchantDashboardController::class, 'index'])->name('dashboard');

    Route::get('/store', [MerchantStoreController::class, 'edit'])->name('store.edit');
    Route::put('/store', [MerchantStoreController::class, 'update'])->name('store.update');

    Route::resource('products', MerchantProductController::class)->except('show');

    Route::get('/orders', [MerchantOrderController::class, 'index'])->name('orders.index');
    Route::patch('/orders/{item}/status', [MerchantOrderController::class, 'updateStatus'])->name('orders.status');
});

// ============ ADMIN ============
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('categories', AdminCategoryController::class)->except('show');

    Route::get('/stores', [AdminStoreController::class, 'index'])->name('stores.index');
    Route::patch('/stores/{store}/status', [AdminStoreController::class, 'updateStatus'])->name('stores.status');

    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
});