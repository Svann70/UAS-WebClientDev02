<?php

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    private static int $n = 0;

    private function customer(): User
    {
        $u = User::create(['name' => 'C', 'email' => 'c@test.id', 'password' => 'rahasia123']);
        $u->role = 'customer';
        $u->save();

        return $u;
    }

    private function product(array $over = []): Product
    {
        self::$n++;
        $n = self::$n;
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

    private function addressPayload(array $over = []): array
    {
        return array_merge(['shipping_address' => 'Jl. Asia Afrika 5, Bandung 40111'], $over);
    }

    public function test_checkout_membuat_order_dan_order_items(): void
    {
        $p = $this->product();
        $c = $this->customer();

        $this->actingAs($c)
            ->withSession(['cart' => [$p->id => 2]])
            ->post('/checkout', $this->addressPayload())
            ->assertRedirect();

        $order = Order::firstOrFail();
        $this->assertSame($c->id, $order->user_id);
        $this->assertEquals(50000, (float) $order->total_amount);
        $this->assertSame('pending', $order->status);
        $this->assertCount(1, $order->items);
        $this->assertEquals(2, $order->items->first()->quantity);
        $this->assertEquals(25000, (float) $order->items->first()->unit_price);
        $this->assertEquals(50000, (float) $order->items->first()->subtotal);
    }

    public function test_stok_berkurang_setelah_checkout(): void
    {
        $p = $this->product(['stock' => 10]);

        $this->actingAs($this->customer())
            ->withSession(['cart' => [$p->id => 3]])
            ->post('/checkout', $this->addressPayload());

        $this->assertEquals(7, $p->fresh()->stock);
    }

    public function test_keranjang_dikosongkan_setelah_checkout(): void
    {
        $p = $this->product();

        $this->actingAs($this->customer())
            ->withSession(['cart' => [$p->id => 1]])
            ->post('/checkout', $this->addressPayload());

        $this->assertEmpty(session('cart', []));
    }

    public function test_harga_diambil_dari_database_bukan_dari_session(): void
    {
        $p = $this->product(['price' => 25000]);

        // Simulasi manipulasi: session membawa harga palsu.
        $this->actingAs($this->customer())
            ->withSession(['cart' => [$p->id => 1, 'price' => 1]])
            ->post('/checkout', $this->addressPayload());

        $order = Order::firstOrFail();
        $this->assertEquals(25000, (float) $order->total_amount);
        $this->assertEquals(25000, (float) $order->items->first()->unit_price);
    }

    public function test_checkout_gagal_kalau_stok_tidak_cukup_dan_tidak_ada_order(): void
    {
        $p = $this->product(['stock' => 1]);

        $this->actingAs($this->customer())
            ->withSession(['cart' => [$p->id => 5]])
            ->post('/checkout', $this->addressPayload());

        // Transaksi di-rollback: tidak ada order setengah jadi, stok tidak berubah.
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
        $this->assertEquals(1, $p->fresh()->stock);
    }

    public function test_tidak_bisa_checkout_keranjang_kosong(): void
    {
        $this->actingAs($this->customer())
            ->post('/checkout', $this->addressPayload())
            ->assertRedirect(route('cart.index'));

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_alamat_wajib_diisi(): void
    {
        $p = $this->product();

        $this->actingAs($this->customer())
            ->withSession(['cart' => [$p->id => 1]])
            ->post('/checkout', ['shipping_address' => ''])
            ->assertSessionHasErrors('shipping_address');
    }

    public function test_order_code_unik_dan_berprefix_gnd(): void
    {
        $p = $this->product();
        $c = $this->customer();

        $this->actingAs($c)->withSession(['cart' => [$p->id => 1]])->post('/checkout', $this->addressPayload());
        $this->actingAs($c)->withSession(['cart' => [$p->id => 1]])->post('/checkout', $this->addressPayload());

        $codes = Order::pluck('order_code')->all();
        $this->assertCount(2, $codes);
        $this->assertSame($codes, array_unique($codes));
        foreach ($codes as $code) {
            $this->assertStringStartsWith('GND-', $code);
        }
    }

    public function test_customer_tidak_bisa_lihat_order_milik_orang_lain(): void
    {
        $p = $this->product();
        $a = $this->customer();

        $this->actingAs($a)->withSession(['cart' => [$p->id => 1]])->post('/checkout', $this->addressPayload());
        $code = Order::firstOrFail()->order_code;

        $b = User::create(['name' => 'B', 'email' => 'b@test.id', 'password' => 'rahasia123']);
        $b->role = 'customer';
        $b->save();

        // Walaupun tahu order_code, bukan pemiliknya -> 404.
        $this->actingAs($b)->get("/pesanan/{$code}")->assertNotFound();
    }

    public function test_merchant_tidak_bisa_checkout(): void
    {
        $m = User::create(['name' => 'M', 'email' => 'mmerch@test.id', 'password' => 'rahasia123']);
        $m->role = 'merchant';
        $m->save();

        $this->actingAs($m)->post('/checkout', $this->addressPayload())->assertForbidden();
    }
}