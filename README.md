# GANDENG: Gerakan Anak Nusantara Dagang
> **Platform Marketplace & Agregator Produk UMKM Lokal**  
> Proyek UAS Web Application Development — Universitas Cakrawala (Semester 3)

---

## 1. Deskripsi Proyek
**GANDENG (Gerakan Anak Nusantara Dagang)** adalah aplikasi web Full-Stack dinamis berbasis server-side MVC menggunakan **Laravel 11** dan **MySQL/MariaDB**. Aplikasi ini dirancang sebagai platform agregator UMKM yang menghubungkan pelaku usaha mikro/kecil dari berbagai daerah dengan pembeli, dilengkapi pembagian hak akses peran (*Role-Based Access Control*), operasi CRUD multi-entitas, katalog produk dinamis, transaksi checkout terisolasi (*database transactions*), dan proteksi keamanan tingkat aplikasi terhadap kerentanan web modern.

---

## 2. Fitur Utama & Pembagian Peran (RBAC)

### A. Publik / Pengunjung (Guest)
- **Katalog Produk Dinamis**: Pencarian produk, filter kategori, pengurutan harga (*termurah / termahal / terbaru*), dan paginasi yang mempertahankan parameter query.
- **Profil Toko Mitra**: Halaman etalase khusus per UMKM lokal.
- **Detail Produk**: Foto, deskripsi dengan sanitasi anti-XSS, harga, stok terkini, dan lokasi toko.
- **Autentikasi**: Pendaftaran akun baru (*Customer / Merchant*) dan login berbasis sesi.

### B. Pembeli (Customer)
- **Keranjang Belanja (Cart)**: Manajemen item berbasis sesi Laravel (tambah, kurang, hapus item) tanpa membebani database sebelum checkout.
- **Checkout Transaksional**: Pengisian alamat pengiriman dengan penguncian baris database (`lockForUpdate`) untuk mencegah *race condition* dan menjamin harga diambil dari basis data asli (*anti-tampering*).
- **Riwayat Pesanan**: Melihat daftar dan status transaksi (`Pending`, `Paid`, `Processing`, `Shipped`, `Completed`, `Cancelled`).
- **Unggah Bukti Bayar**: Konfirmasi pembayaran transfer bank dengan upload gambar aman (*MIME & magic bytes validation*).

### C. Pemilik UMKM (Merchant)
- **Setup Profil Toko**: Kelola nama toko, deskripsi, kota asal, alamat fisik, dan logo toko.
- **CRUD Produk Toko**: Tambah, lihat, ubah, dan hapus produk milik toko sendiri.
- **Keamanan Anti-IDOR**: Merchant dibatasi secara ketat hanya dapat memodifikasi produk dan melihat pesanan tokonya sendiri.
- **Monitoring Pesanan Masuk**: Melacak rincian pembelian item produknya dan memperbarui status pesanan secara bertahap (`Paid -> Processing -> Shipped`).
- **Dashboard Merchant**: 4 kartu ringkasan omzet, total produk, pesanan masuk, dan peringatan stok menipis (≤ 5 unit).

### D. Pengelola Platform (Super Admin)
- **CRUD Kategori Platform**: Mengelola kategori produk global beserta slug otomatis anti-bentrok.
- **Manajemen & Verifikasi Mitra UMKM**: Memoderasi toko mitra (`Active`, `Pending`, `Suspended`). Penangguhan toko otomatis menyembunyikan seluruh produknya dari katalog publik.
- **Pengawasan Transaksi Global**: Memantau seluruh arus pesanan lintas mitra dan memeriksa bukti transfer pembeli.
- **Dashboard Platform**: Ringkasan volume omzet platform, total mitra, dan metrik sistem.

---

## 3. Desain Basis Data & Skema ERD

Aplikasi mengimplementasikan 6 tabel relasional ternormalisasi (3NF):

```
[ users ]
   │ 1
   ├───────────────(1) [ stores ]
   │                     │ 1
   │ 1                   ├───────────────(N) [ products ]
   │                     │                     │ 1
   │                     │                     ├───────────────(N) [ order_items ]
   │                     │ 1                   │                     │ N
   ├───────────────(N) [ orders ] (N)──────────┘                     │
   │                                                                 │
[ categories ] (1)───────────────────────────────────────────────────┘ (N)
```

1. **`users`**: `id`, `name`, `email`, `password`, `role` (`admin`/`merchant`/`customer`), `phone`, `timestamps`.
2. **`stores`**: `id`, `user_id` (FK `users.id` cascade), `name`, `slug`, `description`, `city`, `address`, `logo`, `status` (`pending`/`active`/`suspended`), `timestamps`.
3. **`categories`**: `id`, `name`, `slug`, `icon`, `timestamps`.
4. **`products`**: `id`, `store_id` (FK `stores.id` cascade), `category_id` (FK `categories.id` restrict), `name`, `slug`, `description`, `price`, `stock`, `image`, `is_active`, `timestamps`.
5. **`orders`**: `id`, `user_id` (FK `users.id` restrict), `order_code`, `total_amount`, `status`, `shipping_address`, `payment_receipt`, `notes`, `timestamps`.
6. **`order_items`**: `id`, `order_id` (FK `orders.id` cascade), `product_id` (FK `products.id` restrict), `store_id` (FK `stores.id` cascade), `quantity`, `unit_price`, `subtotal`, `timestamps`.

---

## 4. Arsitektur MVC & Keamanan Aplikasi

- **Pattern MVC**: Pemisahan jelas antara rute (`routes/web.php`), pengendali logika (`app/Http/Controllers`), model relasional Eloquent (`app/Models`), dan antarmuka Blade (`resources/views`).
- **Proteksi CSRF**: Seluruh formulir perubahan data (`POST`, `PUT`, `PATCH`, `DELETE`) dilindungi token `@csrf`.
- **Pencegahan XSS**: Rendering tampilan menggunakan sintaks Blade kurung ganda `{{ ... }}` yang meng-escape output HTML secara otomatis. Deskripsi produk menggunakan kombinasi `nl2br(e(...))`.
- **Pencegahan SQL Injection**: Seluruh operasi query basis data menggunakan Eloquent ORM dan PDO *parameterized prepared statements*.
- **Pencegahan Upload RCE**: Validasi berkas menggunakan rule `image` dan `mimes:jpg,jpeg,png,webp` (memeriksa *magic bytes* internal berkas, bukan sekadar ekstensi). Berkas disimpan dengan penamaan hash acak unik di storage publik.
- **Pencegahan IDOR & Privilege Escalation**:
  - Kolom `role` dan `status` dikecualikan dari `$fillable` Mass Assignment.
  - Setiap akses modifikasi produk divalidasi kepemilikannya terhadap `store_id` milik merchant yang sedang login.
  - Endpoint transaksi publik menggunakan `order_code` acak alih-alih `id` sekuensial.

---

## 5. Akun Pengguna Uji Coba (Demo Credentials)

Semua akun menggunakan kata sandi bawaan: **`password123`**

| Peran (Role) | Email | Deskripsi Akun & Toko |
|---|---|---|
| **Super Admin** | `admin@gandeng.id` | Pengelola utama platform GANDENG |
| **Merchant 1** | `siti@dapurminang.id` | Pemilik Toko: **Dapur Minang Asli** (Padang) |
| **Merchant 2** | `bambang@kopipriangan.id` | Pemilik Toko: **Kopi Priangan Lestari** (Bandung) |
| **Customer** | `fauzi@gmail.com` | Pembeli (sudah memiliki 1 riwayat transaksi terbayar) |

---

## 6. Panduan Instalasi & Menjalankan Proyek Lokal

### Prasyarat Sistem:
- PHP >= 8.2 (ekstensi `pdo_mysql`, `mbstring`, `xml`, `curl`, `zip`, `gd`, `bcmath`)
- Composer >= 2.x
- MySQL / MariaDB Server

### Langkah-Langkah:

1. **Clone Repositori**:
   ```bash
   git clone https://github.com/Svann70/UAS-WebClientDev02.git
   cd UAS-WebClientDev02
   ```

2. **Instal Dependensi**:
   ```bash
   composer install
   ```

3. **Konfigurasi Environment**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Sesuaikan kredensial basis data pada berkas `.env`:
   ```ini
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=gandeng_db
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Migrasi & Seeding Data**:
   Buat database `gandeng_db` di MySQL Anda, lalu jalankan:
   ```bash
   php artisan migrate:fresh --seed
   ```
   *Alternatif:* Import langsung berkas `database/gandeng_db.sql` ke database MySQL Anda.

5. **Hubungkan Storage File Upload**:
   ```bash
   php artisan storage:link
   ```

6. **Jalankan Automated Test Suite**:
   Untuk memverifikasi 88 unit & feature tests:
   ```bash
   php artisan test
   ```

7. **Jalankan Server Lokal**:
   ```bash
   php artisan serve
   ```
   Buka peramban di: `http://localhost:8000`

---

## 7. Deliverables Mahasiswa
- **Source Code**: Repositori GitHub `https://github.com/Svann70/UAS-WebClientDev02`
- **Database Dump**: `database/gandeng_db.sql`
- **Konfigurasi Environment**: `.env.example`
- **Dokumentasi & Panduan**: `README.md`
- **Automated Tests**: 88 unit & feature tests lulus (100% assertions green)
