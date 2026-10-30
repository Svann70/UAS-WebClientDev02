<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MerchantProductTest extends TestCase
{
    use RefreshDatabase;

    private function merchantWithStore(string $suffix = 'a'): User
    {
        $u = User::create(['name' => "M{$suffix}", 'email' => "m{$suffix}@test.id", 'password' => 'rahasia123']);
        $u->role = 'merchant';
        $u->save();
        Store::create([
            'user_id' => $u->id, 'name' => "Toko {$suffix}", 'slug' => "toko-{$suffix}",
            'city' => 'Bandung', 'address' => 'Jl. Test 1',
        ]);

        return $u;
    }

    private function payload(array $over = []): array
    {
        return array_merge([
            'category_id' => Category::create(['name' => 'Keripik ' . uniqid(), 'slug' => 'k-' . uniqid()])->id,
            'name' => 'Keripik Balado ' . uniqid(),
            'description' => 'Pedas gurih khas Minang',
            'price' => 25000,
            'stock' => 50,
        ], $over);
    }

    public function test_merchant_bisa_tambah_produk(): void
    {
        $m = $this->merchantWithStore();
        $data = $this->payload();

        $this->actingAs($m)->post('/merchant/products', $data)
            ->assertRedirect(route('merchant.products.index'));

        $this->assertDatabaseHas('products', ['name' => $data['name'], 'store_id' => $m->store->id]);
    }

    public function test_store_id_tidak_bisa_dipalsukan_dari_form(): void
    {
        $m = $this->merchantWithStore('a');
        $lain = $this->merchantWithStore('b');

        $this->actingAs($m)->post('/merchant/products', $this->payload(['store_id' => $lain->store->id]));

        // store_id tetap milik si pengirim, bukan yang dikirim di form.
        $this->assertDatabaseMissing('products', ['store_id' => $lain->store->id]);
        $this->assertDatabaseHas('products', ['store_id' => $m->store->id]);
    }

    public function test_merchant_tidak_bisa_edit_produk_toko_lain(): void
    {
        $a = $this->merchantWithStore('a');
        $b = $this->merchantWithStore('b');

        $this->actingAs($a)->post('/merchant/products', $this->payload());
        $produkB = Product::where('store_id', $b->store->id)->first()
            ?? Product::create([
                'store_id' => $b->store->id,
                'category_id' => Category::create(['name' => 'X', 'slug' => 'x'])->id,
                'name' => 'Produk B', 'slug' => 'produk-b', 'description' => 'd', 'price' => 1000, 'stock' => 1,
            ]);

        $this->actingAs($a)->get("/merchant/products/{$produkB->id}/edit")->assertForbidden();
        $this->actingAs($a)->put("/merchant/products/{$produkB->id}", $this->payload())->assertForbidden();
        $this->actingAs($a)->delete("/merchant/products/{$produkB->id}")->assertForbidden();
    }

    public function test_upload_gambar_disimpan_dengan_nama_acak(): void
    {
        Storage::fake('public');
        $m = $this->merchantWithStore();

        $this->actingAs($m)->post('/merchant/products', $this->payload([
            'image' => UploadedFile::fake()->image('foto produk saya.png', 400, 400),
        ]))->assertRedirect();

        $product = Product::where('store_id', $m->store->id)->firstOrFail();
        $this->assertNotNull($product->image);
        // Nama asli tidak boleh muncul di path.
        $this->assertStringNotContainsString('foto produk saya', $product->image);
        Storage::disk('public')->assertExists($product->image);
    }

    public function test_file_bukan_gambar_ditolak(): void
    {
        Storage::fake('public');
        $m = $this->merchantWithStore();

        $this->actingAs($m)->post('/merchant/products', $this->payload([
            'image' => UploadedFile::fake()->create('shell.php', 10, 'application/x-php'),
        ]))->assertSessionHasErrors('image');

        $this->assertDatabaseCount('products', 0);
    }

    public function test_harga_negatif_ditolak(): void
    {
        $m = $this->merchantWithStore();

        $this->actingAs($m)->post('/merchant/products', $this->payload(['price' => -5]))
            ->assertSessionHasErrors('price');
    }

    public function test_merchant_tanpa_toko_tidak_bisa_akses_produk(): void
    {
        $u = User::create(['name' => 'Tanpa Toko', 'email' => 'tt@test.id', 'password' => 'rahasia123']);
        $u->role = 'merchant';
        $u->save();

        $this->actingAs($u)->get('/merchant/products')->assertForbidden();
    }

    public function test_customer_tidak_bisa_akses_produk_merchant(): void
    {
        $c = User::create(['name' => 'C', 'email' => 'c@test.id', 'password' => 'rahasia123']);
        $c->role = 'customer';
        $c->save();

        $this->actingAs($c)->get('/merchant/products')->assertForbidden();
    }

    public function test_gambar_lama_dihapus_saat_ganti_gambar(): void
    {
        Storage::fake('public');
        $m = $this->merchantWithStore();

        $this->actingAs($m)->post('/merchant/products', $this->payload([
            'image' => UploadedFile::fake()->image('lama.jpg'),
        ]));
        $product = Product::where('store_id', $m->store->id)->firstOrFail();
        $oldPath = $product->image;

        $this->actingAs($m)->put("/merchant/products/{$product->id}", $this->payload([
            'name' => $product->name,
            'image' => UploadedFile::fake()->image('baru.jpg'),
        ]));

        Storage::disk('public')->assertMissing($oldPath);
        $this->assertNotSame($oldPath, $product->fresh()->image);
    }

    public function test_produk_dengan_riwayat_order_tidak_bisa_dihapus(): void
    {
        $m = $this->merchantWithStore();
        $this->actingAs($m)->post('/merchant/products', $this->payload());
        $product = Product::where('store_id', $m->store->id)->firstOrFail();

        $c = User::create(['name' => 'C', 'email' => 'c@test.id', 'password' => 'rahasia123']);
        $c->role = 'customer';
        $c->save();

        $order = \App\Models\Order::create([
            'user_id' => $c->id, 'total_amount' => 25000, 'shipping_address' => 'Jl. X',
        ]);
        \App\Models\OrderItem::create([
            'order_id' => $order->id, 'product_id' => $product->id, 'store_id' => $m->store->id,
            'quantity' => 1, 'unit_price' => 25000, 'subtotal' => 25000,
        ]);

        $this->actingAs($m)->delete("/merchant/products/{$product->id}")->assertSessionHas('error');
        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }
}