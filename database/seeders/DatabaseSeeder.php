<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Pengguna (Admin, Merchant, Customer)
        $admin = User::create([
            'name' => 'Super Admin GANDENG',
            'email' => 'admin@gandeng.id',
            'password' => Hash::make('password123'),
            'phone' => '081234567890',
        ]);
        $admin->role = 'admin';
        $admin->save();

        $merchant1 = User::create([
            'name' => 'Siti Aminah',
            'email' => 'siti@dapurminang.id',
            'password' => Hash::make('password123'),
            'phone' => '081298765431',
        ]);
        $merchant1->role = 'merchant';
        $merchant1->save();

        $merchant2 = User::create([
            'name' => 'Bambang Sudiro',
            'email' => 'bambang@kopipriangan.id',
            'password' => Hash::make('password123'),
            'phone' => '081298765432',
        ]);
        $merchant2->role = 'merchant';
        $merchant2->save();

        $customer = User::create([
            'name' => 'Ahmad Fauzi',
            'email' => 'fauzi@gmail.com',
            'password' => Hash::make('password123'),
            'phone' => '085612345678',
        ]);
        // customer default role

        // 2. Kategori Produk
        $categories = [
            ['name' => 'Keripik & Kerupuk', 'slug' => 'keripik-kerupuk', 'icon' => 'basket'],
            ['name' => 'Sambal Nusantara', 'slug' => 'sambal-nusantara', 'icon' => 'fire'],
            ['name' => 'Kopi & Minuman Tradisional', 'slug' => 'kopi-minuman', 'icon' => 'cup-hot'],
            ['name' => 'Kue & Camilan Khas', 'slug' => 'kue-camilan', 'icon' => 'cake'],
        ];

        $catModels = [];
        foreach ($categories as $cat) {
            $catModels[$cat['slug']] = Category::create($cat);
        }

        // 3. Profil Toko Mitra UMKM
        $store1 = Store::create([
            'user_id' => $merchant1->id,
            'name' => 'Dapur Minang Asli',
            'slug' => 'dapur-minang-asli',
            'logo' => 'stores/dapur-minang-logo.svg',
            'description' => 'Sentra aneka sambal kemasan dan keripik balado asli Padang dengan resep turun temurun.',
            'city' => 'Padang',
            'address' => 'Jl. Khatib Sulaiman No. 45, Kota Padang, Sumatera Barat',
            'status' => 'active',
        ]);

        $store2 = Store::create([
            'user_id' => $merchant2->id,
            'name' => 'Kopi Priangan Lestari',
            'slug' => 'kopi-priangan-lestari',
            'logo' => 'stores/kopi-priangan-logo.svg',
            'description' => 'Biji kopi robusta dan arabika pilihan langsung dari kelompok tani Gunung Puntang Jawa Barat.',
            'city' => 'Bandung',
            'address' => 'Jl. Raya Pangalengan Km 28, Kabupaten Bandung, Jawa Barat',
            'status' => 'active',
        ]);

        // 4. Katalog Produk
        $products = [
            [
                'store_id' => $store1->id,
                'category_id' => $catModels['keripik-kerupuk']->id,
                'name' => 'Keripik Balado Sanjai Merah 250g',
                'slug' => 'keripik-balado-sanjai-merah-250g',
                'image' => 'products/keripik-balado-sanjai.svg',
                'description' => 'Keripik singkong renyah khas Bukittinggi dilapisi bumbu cabai balado basah manis pedas gurih alami tanpa pengawet.',
                'price' => 28000,
                'stock' => 45,
                'is_active' => true,
            ],
            [
                'store_id' => $store1->id,
                'category_id' => $catModels['sambal-nusantara']->id,
                'name' => 'Sambal Ijo Teri Padang Kemasan 200g',
                'slug' => 'sambal-ijo-teri-padang-kemasan-200g',
                'image' => 'products/sambal-ijo-teri.svg',
                'description' => 'Sambal cabai hijau dipadukan dengan gurihnya ikan teri medan goreng renyah dan minyak kelapa murni.',
                'price' => 35000,
                'stock' => 30,
                'is_active' => true,
            ],
            [
                'store_id' => $store1->id,
                'category_id' => $catModels['sambal-nusantara']->id,
                'name' => 'Sambal Cumi Asin Cabe Rawit 200g',
                'slug' => 'sambal-cumi-asin-cabe-rawit-200g',
                'image' => 'products/sambal-cumi-rawit.svg',
                'description' => 'Sambal cumi asin melimpah dengan racikan cabai rawit pedas ekstra. Higienis dan tahan lama.',
                'price' => 42000,
                'stock' => 20,
                'is_active' => true,
            ],
            [
                'store_id' => $store2->id,
                'category_id' => $catModels['kopi-minuman']->id,
                'name' => 'Kopi Arabika Puntang Natural 200g Biji/Bubuk',
                'slug' => 'kopi-arabika-puntang-natural-200g',
                'image' => 'products/kopi-arabika-puntang.svg',
                'description' => 'Single origin specialty coffee dari Gunung Puntang Bandung. Karakter rasa buah manis fruity dengan aroma segar.',
                'price' => 65000,
                'stock' => 25,
                'is_active' => true,
            ],
            [
                'store_id' => $store2->id,
                'category_id' => $catModels['kopi-minuman']->id,
                'name' => 'Kopi Robusta Malabar Honey 250g',
                'slug' => 'kopi-robusta-malabar-honey-250g',
                'image' => 'products/kopi-robusta-malabar.svg',
                'description' => 'Kopi robusta olahan honey proses. Body tebal rasa cokelat karamel pekat, cocok untuk seduh harian atau es kopi susu.',
                'price' => 45000,
                'stock' => 40,
                'is_active' => true,
            ],
            [
                'store_id' => $store2->id,
                'category_id' => $catModels['kue-camilan']->id,
                'name' => 'Gula Semut Jahe Merah Organik 250g',
                'slug' => 'gula-semut-jahe-merah-organik-250g',
                'image' => 'products/gula-semut-jahe.svg',
                'description' => 'Minuman seduh herbal alami dari nira kelapa murni dan ekstrak rimpang jahe merah petani lokal.',
                'price' => 32000,
                'stock' => 50,
                'is_active' => true,
            ],
        ];

        $prodModels = [];
        foreach ($products as $prod) {
            $prodModels[] = Product::create($prod);
        }

        // 5. Data Transaksi Sampel (Order & OrderItem)
        $order = Order::create([
            'user_id' => $customer->id,
            'total_amount' => 91000,
            'shipping_address' => "Ahmad Fauzi\nJl. Dago Asri No. 12, Coblong, Kota Bandung, Jawa Barat 40135\nHP: 085612345678",
            'notes' => 'Tolong packing ekstra bubble wrap ya min.',
        ]);
        $order->status = 'paid';
        $order->order_code = 'GND-20261028-DEMO1';
        $order->save();

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $prodModels[0]->id,
            'store_id' => $store1->id,
            'quantity' => 1,
            'unit_price' => 28000,
            'subtotal' => 28000,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $prodModels[3]->id,
            'store_id' => $store2->id,
            'quantity' => 1,
            'unit_price' => 63000,
            'subtotal' => 63000,
        ]);
    }
}
