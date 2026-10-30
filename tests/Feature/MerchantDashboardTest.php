<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MerchantDashboardTest extends TestCase
{
    use RefreshDatabase;

    private function storeWithProduct(array $over = []): Store
    {
        $u = User::create(['name' => 'M', 'email' => 'm@test.id', 'password' => 'rahasia123']);
        $u->role = 'merchant';
        $u->save();

        $store = Store::create([
            'user_id' => $u->id, 'name' => 'Toko Sari', 'slug' => 'toko-sari',
            'city' => 'Bandung', 'address' => 'Jl. X',
        ]);

        Product::create(array_merge([
            'store_id' => $store->id,
            'category_id' => Category::firstOrCreate(['slug' => 'k'], ['name' => 'K'])->id,
            'name' => 'Keripik', 'slug' => 'keripik-dash',
            'description' => 'd', 'price' => 25000, 'stock' => 3, 'is_active' => true,
        ], $over));

        return $store;
    }

    public function test_dashboard_menampilkan_ringkasan(): void
    {
        $store = $this->storeWithProduct();

        $this->actingAs($store->user)
            ->get('/merchant/dashboard')
            ->assertOk()
            ->assertSee('Toko Sari')
            ->assertSee('Keripik')
            ->assertSee('Stok Menipis');
    }

    public function test_merchant_tanpa_toko_403(): void
    {
        $u = User::create(['name' => 'M', 'email' => 'm@test.id', 'password' => 'rahasia123']);
        $u->role = 'merchant';
        $u->save();

        $this->actingAs($u)->get('/merchant/dashboard')->assertForbidden();
    }
}