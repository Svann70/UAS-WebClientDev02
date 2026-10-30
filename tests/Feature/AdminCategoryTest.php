<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCategoryTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $u = User::create(['name' => 'Admin', 'email' => 'admin@test.id', 'password' => 'rahasia123']);
        $u->role = 'admin';
        $u->save();

        return $u;
    }

    public function test_admin_bisa_lihat_daftar_kategori(): void
    {
        Category::create(['name' => 'Keripik', 'slug' => 'keripik']);

        $this->actingAs($this->admin())
            ->get('/admin/categories')
            ->assertOk()
            ->assertSee('Keripik');
    }

    public function test_admin_bisa_tambah_kategori_dan_slug_otomatis(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/categories', ['name' => 'Kopi Lokal'])
            ->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseHas('categories', ['name' => 'Kopi Lokal', 'slug' => 'kopi-lokal']);
    }

    public function test_slug_bentrok_dapat_suffix_angka(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->post('/admin/categories', ['name' => 'Sambal']);
        $this->actingAs($admin)->post('/admin/categories', ['name' => 'Sambal']);

        // Nama kedua ditolak unique rule, jadi hanya ada satu.
        $this->assertDatabaseCount('categories', 1);

        // Slug duplikat hanya mungkin kalau nama beda tapi slug sama (mis. "Kopi Lokal" vs "Kopi  Lokal").
        $this->actingAs($admin)->post('/admin/categories', ['name' => 'Kopi Lokal']);
        $this->actingAs($admin)->post('/admin/categories', ['name' => 'Kopi  Lokal']);
        $this->assertDatabaseHas('categories', ['slug' => 'kopi-lokal']);
        $this->assertDatabaseHas('categories', ['slug' => 'kopi-lokal-2']);
    }

    public function test_nama_kategori_wajib_diisi(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/categories', ['name' => ''])
            ->assertSessionHasErrors('name');
    }

    public function test_admin_bisa_edit_kategori(): void
    {
        $cat = Category::create(['name' => 'Lama', 'slug' => 'lama']);

        $this->actingAs($this->admin())
            ->put("/admin/categories/{$cat->id}", ['name' => 'Baru'])
            ->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseHas('categories', ['id' => $cat->id, 'name' => 'Baru', 'slug' => 'baru']);
    }

    public function test_kategori_terpakai_produk_tidak_bisa_dihapus(): void
    {
        $admin = $this->admin();
        $cat = Category::create(['name' => 'Keripik', 'slug' => 'keripik']);

        $merchant = User::create(['name' => 'M', 'email' => 'm@test.id', 'password' => 'rahasia123']);
        $merchant->role = 'merchant';
        $merchant->save();
        $store = Store::create(['user_id' => $merchant->id, 'name' => 'Toko', 'slug' => 'toko', 'city' => 'Bandung', 'address' => 'Jl. 1']);
        Product::create(['store_id' => $store->id, 'category_id' => $cat->id, 'name' => 'P', 'slug' => 'p', 'description' => 'd', 'price' => 1000, 'stock' => 5]);

        $this->actingAs($admin)
            ->delete("/admin/categories/{$cat->id}")
            ->assertSessionHas('error');

        $this->assertDatabaseHas('categories', ['id' => $cat->id]);
    }

    public function test_kategori_kosong_bisa_dihapus(): void
    {
        $cat = Category::create(['name' => 'Kosong', 'slug' => 'kosong']);

        $this->actingAs($this->admin())
            ->delete("/admin/categories/{$cat->id}")
            ->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseMissing('categories', ['id' => $cat->id]);
    }

    public function test_merchant_tidak_bisa_akses_crud_kategori(): void
    {
        $m = User::create(['name' => 'M', 'email' => 'm@test.id', 'password' => 'rahasia123']);
        $m->role = 'merchant';
        $m->save();

        $this->actingAs($m)->get('/admin/categories')->assertForbidden();
        $this->actingAs($m)->post('/admin/categories', ['name' => 'Nakal'])->assertForbidden();
    }
}