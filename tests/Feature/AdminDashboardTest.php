<?php

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    private static int $n = 0;

    private function admin(): User
    {
        $u = User::create(['name' => 'Admin', 'email' => 'admin@test.id', 'password' => 'rahasia123']);
        $u->role = 'admin';
        $u->save();

        return $u;
    }

    private function store(string $status = 'active'): Store
    {
        self::$n++;
        $n = self::$n;
        $u = User::create(['name' => "M{$n}", 'email' => "m{$n}@test.id", 'password' => 'rahasia123']);
        $u->role = 'merchant';
        $u->save();

        $store = Store::create([
            'user_id' => $u->id, 'name' => "Toko {$n}", 'slug' => "toko-{$n}",
            'city' => 'Bandung', 'address' => 'Jl. X',
        ]);

        if ($status !== 'active') {
            $store->status = $status;
            $store->save();
        }

        return $store;
    }

    public function test_dashboard_admin_menampilkan_statistik(): void
    {
        $this->store('active');
        $this->store('pending');

        $this->actingAs($this->admin())
            ->get('/admin/dashboard')
            ->assertOk()
            ->assertSee('Dashboard Platform')
            ->assertSee('Mitra Aktif')
            ->assertSee('menunggu verifikasi');
    }

    public function test_merchant_tidak_bisa_akses_dashboard_admin(): void
    {
        $s = $this->store();
        $this->actingAs($s->user)->get('/admin/dashboard')->assertForbidden();
    }

    public function test_admin_bisa_verifikasi_toko_pending(): void
    {
        $store = $this->store('pending');
        $admin = $this->admin();

        $this->actingAs($admin)
            ->patch("/admin/stores/{$store->id}/status", ['status' => 'active'])
            ->assertSessionHas('success');

        $this->assertSame('active', $store->fresh()->status);
    }

    public function test_admin_bisa_tangguhkan_toko(): void
    {
        $store = $this->store('active');
        $admin = $this->admin();

        $this->actingAs($admin)
            ->patch("/admin/stores/{$store->id}/status", ['status' => 'suspended'])
            ->assertSessionHas('success');

        $this->assertSame('suspended', $store->fresh()->status);
    }

    public function test_produk_toko_suspend_hilang_dari_katalog(): void
    {
        $store = $this->store('active');
        $cat = Category::create(['name' => 'K', 'slug' => 'k-cat']);
        $p = Product::create([
            'store_id' => $store->id, 'category_id' => $cat->id, 'name' => 'Produk Suspend',
            'slug' => 'produk-suspend', 'description' => 'd', 'price' => 1000, 'stock' => 5, 'is_active' => true,
        ]);

        $this->get('/produk')->assertSee('Produk Suspend');

        $this->actingAs($this->admin())
            ->patch("/admin/stores/{$store->id}/status", ['status' => 'suspended']);

        $this->get('/produk')->assertDontSee('Produk Suspend');
    }

    public function test_status_tidak_valid_ditolak(): void
    {
        $store = $this->store();

        $this->actingAs($this->admin())
            ->patch("/admin/stores/{$store->id}/status", ['status' => 'hapus'])
            ->assertSessionHasErrors('status');
    }

    public function test_admin_bisa_lihat_daftar_pesanan(): void
    {
        $s = $this->store();
        $c = User::create(['name' => 'C', 'email' => 'c@test.id', 'password' => 'rahasia123']);
        $c->role = 'customer';
        $c->save();
        $order = Order::create(['user_id' => $c->id, 'total_amount' => 50000, 'shipping_address' => 'Jl. A']);

        $this->actingAs($this->admin())
            ->get('/admin/orders')
            ->assertOk()
            ->assertSee($order->order_code);
    }

    public function test_admin_bisa_lihat_detail_pesanan(): void
    {
        $s = $this->store();
        $cat = Category::create(['name' => 'K2', 'slug' => 'k2']);
        $p = Product::create([
            'store_id' => $s->id, 'category_id' => $cat->id, 'name' => 'Produk Detail',
            'slug' => 'produk-detail', 'description' => 'd', 'price' => 10000, 'stock' => 5, 'is_active' => true,
        ]);
        $c = User::create(['name' => 'Budi', 'email' => 'b@test.id', 'password' => 'rahasia123']);
        $c->role = 'customer';
        $c->save();
        $order = Order::create(['user_id' => $c->id, 'total_amount' => 20000, 'shipping_address' => 'Jl. B']);
        $order->items()->create([
            'product_id' => $p->id, 'store_id' => $s->id, 'quantity' => 2,
            'unit_price' => 10000, 'subtotal' => 20000,
        ]);

        $this->actingAs($this->admin())
            ->get("/admin/orders/{$order->id}")
            ->assertOk()
            ->assertSee($order->order_code)
            ->assertSee('Budi')
            ->assertSee('Produk Detail');
    }

    public function test_customer_tidak_bisa_akses_admin(): void
    {
        $c = User::create(['name' => 'C', 'email' => 'c2@test.id', 'password' => 'rahasia123']);
        $c->role = 'customer';
        $c->save();

        $this->actingAs($c)->get('/admin/stores')->assertForbidden();
        $this->actingAs($c)->get('/admin/orders')->assertForbidden();
    }
}