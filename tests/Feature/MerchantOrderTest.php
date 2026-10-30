<?php

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MerchantOrderTest extends TestCase
{
    use RefreshDatabase;

    private static int $n = 0;

    private function merchantStore(): Store
    {
        self::$n++;
        $n = self::$n;
        $u = User::create(['name' => "M{$n}", 'email' => "m{$n}@test.id", 'password' => 'rahasia123']);
        $u->role = 'merchant';
        $u->save();

        return Store::create([
            'user_id' => $u->id, 'name' => "Toko {$n}", 'slug' => "toko-{$n}",
            'city' => 'Bandung', 'address' => 'Jl. X',
        ]);
    }

    private function product(Store $store, array $over = []): Product
    {
        self::$n++;
        $n = self::$n;
        $cat = Category::firstOrCreate(['slug' => 'keripik'], ['name' => 'Keripik']);

        return Product::create(array_merge([
            'store_id' => $store->id, 'category_id' => $cat->id,
            'name' => "Produk {$n}", 'slug' => "produk-{$n}",
            'description' => 'd', 'price' => 25000, 'stock' => 10, 'is_active' => true,
        ], $over));
    }

    private function orderWithStatus(Product $product, string $status, int $qty = 1): OrderItem
    {
        $c = User::create(['name' => 'C', 'email' => 'c' . uniqid() . '@test.id', 'password' => 'rahasia123']);
        $c->role = 'customer';
        $c->save();

        $order = Order::create([
            'user_id' => $c->id,
            'total_amount' => (float) $product->price * $qty,
            'shipping_address' => 'Jl. Test 1',
        ]);
        $order->status = $status;
        $order->save();

        return $order->items()->create([
            'product_id' => $product->id,
            'store_id' => $product->store_id,
            'quantity' => $qty,
            'unit_price' => $product->price,
            'subtotal' => (float) $product->price * $qty,
        ]);
    }

    public function test_merchant_hanya_melihat_pesanan_tokonya(): void
    {
        $storeA = $this->merchantStore();
        $storeB = $this->merchantStore();

        $itemA = $this->orderWithStatus($this->product($storeA), 'paid');
        $itemB = $this->orderWithStatus($this->product($storeB), 'paid');

        $html = $this->actingAs($storeA->user)->get('/merchant/orders')->getContent();

        // Hanya kode pesanan A yang tampil, kode pesanan B tidak.
        $this->assertStringContainsString($itemA->order->order_code, $html);
        $this->assertStringNotContainsString($itemB->order->order_code, $html);
    }

    public function test_omzet_hanya_menghitung_pesanan_lunas(): void
    {
        $store = $this->merchantStore();
        $p = $this->product($store, ['price' => 25000]);

        $this->orderWithStatus($p, 'paid', 2);      // 50000 -> dihitung
        $this->orderWithStatus($p, 'pending', 3);   // 75000 -> tidak dihitung
        $this->orderWithStatus($p, 'cancelled', 1); // 25000 -> tidak dihitung

        $this->actingAs($store->user)->get('/merchant/orders')
            ->assertSee('Rp 50.000', false);
    }

    public function test_merchant_bisa_ubah_status_paid_ke_processing(): void
    {
        $store = $this->merchantStore();
        $item = $this->orderWithStatus($this->product($store), 'paid');

        $this->actingAs($store->user)
            ->patch("/merchant/orders/{$item->id}/status", ['status' => 'processing'])
            ->assertSessionHas('success');

        $this->assertSame('processing', $item->order->fresh()->status);
    }

    public function test_transisi_status_tidak_boleh_lompat(): void
    {
        $store = $this->merchantStore();
        $item = $this->orderWithStatus($this->product($store), 'paid');

        // paid -> completed (lompat) harus ditolak.
        $this->actingAs($store->user)
            ->patch("/merchant/orders/{$item->id}/status", ['status' => 'completed'])
            ->assertSessionHas('error');

        $this->assertSame('paid', $item->order->fresh()->status);
    }

    public function test_pesanan_belum_dibayar_tidak_bisa_diproses(): void
    {
        $store = $this->merchantStore();
        $item = $this->orderWithStatus($this->product($store), 'pending');

        $this->actingAs($store->user)
            ->patch("/merchant/orders/{$item->id}/status", ['status' => 'processing'])
            ->assertSessionHas('error');

        $this->assertSame('pending', $item->order->fresh()->status);
    }

    public function test_merchant_tidak_bisa_ubah_status_pesanan_toko_lain(): void
    {
        $storeA = $this->merchantStore();
        $storeB = $this->merchantStore();
        $itemB = $this->orderWithStatus($this->product($storeB), 'paid');

        $this->actingAs($storeA->user)
            ->patch("/merchant/orders/{$itemB->id}/status", ['status' => 'processing'])
            ->assertForbidden();

        $this->assertSame('paid', $itemB->order->fresh()->status);
    }

    public function test_status_tidak_valid_ditolak(): void
    {
        $store = $this->merchantStore();
        $item = $this->orderWithStatus($this->product($store), 'paid');

        $this->actingAs($store->user)
            ->patch("/merchant/orders/{$item->id}/status", ['status' => 'pending'])
            ->assertSessionHasErrors('status');
    }

    public function test_customer_tidak_bisa_akses_pesanan_merchant(): void
    {
        $c = User::create(['name' => 'C', 'email' => 'cx@test.id', 'password' => 'rahasia123']);
        $c->role = 'customer';
        $c->save();

        $this->actingAs($c)->get('/merchant/orders')->assertForbidden();
    }
}