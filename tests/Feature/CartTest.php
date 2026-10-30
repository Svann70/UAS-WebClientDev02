<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    private function customer(): User
    {
        $u = User::create(['name' => 'C', 'email' => 'c@test.id', 'password' => 'rahasia123']);
        $u->role = 'customer';
        $u->save();

        return $u;
    }

    private function product(array $over = []): Product
    {
        static $n = 0;
        $n++;
        $m = User::create(['name' => "M{$n}", 'email' => "m{$n}@test.id", 'password' => 'rahasia123']);
        $m->role = 'merchant';
        $m->save();
        $store = Store::create([
            'user_id' => $m->id, 'name' => "Toko {$n}", 'slug' => "toko-{$n}",
            'city' => 'Bandung', 'address' => 'Jl. X',
        ]);
        $cat = Category::firstOrCreate(['slug' => 'keripik'], ['name' => 'Keripik']);

        return Product::create(array_merge([
            'store_id' => $store->id, 'category_id' => $cat->id,
            'name' => "Produk {$n}", 'slug' => "produk-{$n}",
            'description' => 'd', 'price' => 25000, 'stock' => 10, 'is_active' => true,
        ], $over));
    }

    public function test_customer_bisa_tambah_ke_keranjang(): void
    {
        $p = $this->product();

        $this->actingAs($this->customer())
            ->post("/keranjang/{$p->id}", ['quantity' => 2])
            ->assertRedirect();

        $this->assertEquals([$p->id => 2], session('cart'));
    }

    public function test_jumlah_terakumulasi_saat_tambah_dua_kali(): void
    {
        $p = $this->product();
        $c = $this->customer();

        $this->actingAs($c)->post("/keranjang/{$p->id}", ['quantity' => 2]);
        $this->actingAs($c)->post("/keranjang/{$p->id}", ['quantity' => 3]);

        $this->assertEquals([$p->id => 5], session('cart'));
    }

    public function test_jumlah_dibatasi_stok(): void
    {
        $p = $this->product(['stock' => 3]);

        $this->actingAs($this->customer())->post("/keranjang/{$p->id}", ['quantity' => 99]);

        $this->assertEquals([$p->id => 3], session('cart'));
    }

    public function test_tidak_bisa_tambah_produk_nonaktif(): void
    {
        $p = $this->product(['is_active' => false]);

        $this->actingAs($this->customer())
            ->post("/keranjang/{$p->id}", ['quantity' => 1])
            ->assertNotFound();

        $this->assertEmpty(session('cart', []));
    }

    public function test_tidak_bisa_tambah_produk_stok_habis(): void
    {
        $p = $this->product(['stock' => 0]);

        $this->actingAs($this->customer())
            ->post("/keranjang/{$p->id}", ['quantity' => 1])
            ->assertStatus(400);
    }

    public function test_quantity_nol_menghapus_item(): void
    {
        $p = $this->product();
        $c = $this->customer();

        $this->actingAs($c)->post("/keranjang/{$p->id}", ['quantity' => 2]);
        $this->actingAs($c)->patch("/keranjang/{$p->id}", ['quantity' => 0]);

        $this->assertEmpty(session('cart', []));
    }

    public function test_hapus_item_dari_keranjang(): void
    {
        $p = $this->product();
        $c = $this->customer();

        $this->actingAs($c)->post("/keranjang/{$p->id}", ['quantity' => 1]);
        $this->actingAs($c)->delete("/keranjang/{$p->id}");

        $this->assertEmpty(session('cart', []));
    }

    public function test_halaman_keranjang_menampilkan_total_benar(): void
    {
        $p = $this->product(['price' => 25000]);

        $this->actingAs($this->customer())
            ->withSession(['cart' => [$p->id => 3]])
            ->get('/keranjang')
            ->assertOk()
            ->assertSee('Rp 75.000', false);
    }

    public function test_merchant_tidak_bisa_akses_keranjang(): void
    {
        $m = User::create(['name' => 'M', 'email' => 'm@test.id', 'password' => 'rahasia123']);
        $m->role = 'merchant';
        $m->save();

        $this->actingAs($m)->get('/keranjang')->assertForbidden();
    }

    public function test_guest_diarahkan_ke_login(): void
    {
        $this->get('/keranjang')->assertRedirect('/login');
    }
}