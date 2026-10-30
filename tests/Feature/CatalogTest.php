<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use RefreshDatabase;

    private function store(string $status = 'active'): Store
    {
        static $n = 0;
        $n++;
        $u = User::create(['name' => "M{$n}", 'email' => "m{$n}@test.id", 'password' => 'rahasia123']);
        $u->role = 'merchant';
        $u->save();

        $store = Store::create([
            'user_id' => $u->id, 'name' => "Toko {$n}", 'slug' => "toko-{$n}",
            'city' => 'Bandung', 'address' => 'Jl. Test',
        ]);

        // `status` sengaja tidak fillable (hanya admin yang boleh ubah),
        // jadi diset eksplisit di sini untuk kebutuhan test.
        if ($status !== 'active') {
            $store->status = $status;
            $store->save();
        }

        return $store;
    }

    private function product(Store $store, array $over = []): Product
    {
        static $n = 0;
        $n++;
        $cat = Category::firstOrCreate(['slug' => 'keripik'], ['name' => 'Keripik']);

        return Product::create(array_merge([
            'store_id' => $store->id,
            'category_id' => $cat->id,
            'name' => "Produk {$n}",
            'slug' => "produk-{$n}",
            'description' => 'Deskripsi produk',
            'price' => 25000,
            'stock' => 10,
            'is_active' => true,
        ], $over));
    }

    public function test_home_menampilkan_produk_aktif(): void
    {
        $p = $this->product($this->store());

        $this->get('/')->assertOk()->assertSee($p->name);
    }

    public function test_produk_nonaktif_tidak_muncul_di_katalog(): void
    {
        $p = $this->product($this->store(), ['is_active' => false]);

        $this->get('/produk')->assertOk()->assertDontSee($p->name);
    }

    public function test_produk_dari_toko_suspend_tidak_muncul(): void
    {
        $p = $this->product($this->store('suspended'));

        $this->get('/produk')->assertOk()->assertDontSee($p->name);
    }

    public function test_detail_produk_nonaktif_404(): void
    {
        $p = $this->product($this->store(), ['is_active' => false]);

        $this->get("/produk/{$p->slug}")->assertNotFound();
    }

    public function test_pencarian_nama_produk(): void
    {
        $s = $this->store();
        $this->product($s, ['name' => 'Keripik Balado', 'slug' => 'keripik-balado']);
        $this->product($s, ['name' => 'Sambal Bawang', 'slug' => 'sambal-bawang']);

        $this->get('/produk?q=balado')->assertSee('Keripik Balado')->assertDontSee('Sambal Bawang');
    }

    public function test_filter_kategori(): void
    {
        $s = $this->store();
        $keripik = Category::create(['name' => 'Keripik', 'slug' => 'keripik-x']);
        $sambal = Category::create(['name' => 'Sambal', 'slug' => 'sambal-x']);

        $this->product($s, ['name' => 'Kripik A', 'slug' => 'kripik-a', 'category_id' => $keripik->id]);
        $this->product($s, ['name' => 'Sambal A', 'slug' => 'sambal-a', 'category_id' => $sambal->id]);

        $this->get('/produk?category=' . $sambal->slug)
            ->assertSee('Sambal A')
            ->assertDontSee('Kripik A');
    }

    public function test_urutkan_harga_termurah(): void
    {
        $s = $this->store();
        $this->product($s, ['name' => 'Mahal', 'slug' => 'mahal', 'price' => 90000]);
        $this->product($s, ['name' => 'Murah', 'slug' => 'murah', 'price' => 5000]);

        $html = $this->get('/produk?sort=termurah')->getContent();
        $this->assertLessThan(strpos($html, 'Mahal'), strpos($html, 'Murah'));
    }

    public function test_pencarian_tidak_kena_sql_injection(): void
    {
        $s = $this->store();
        $p = $this->product($s, ['name' => 'Keripik', 'slug' => 'keripik-q']);

        // Payload klasik: kalau query di-concat, ini akan error 500 atau membocorkan semua data.
        // Dicek lewat link produk (bukan teks "Keripik") karena nama kategori juga tampil di filter.
        $this->get("/produk?q=" . urlencode("' OR 1=1 --"))
            ->assertOk()
            ->assertDontSee(route('catalog.show', $p->slug), false);
    }

    public function test_input_sort_tidak_valid_ditolak(): void
    {
        $this->get('/produk?sort=DROP TABLE users')->assertSessionHasErrors('sort');
    }

    public function test_halaman_toko_menampilkan_produk_toko(): void
    {
        $s = $this->store();
        $p = $this->product($s);

        $this->get("/toko/{$s->slug}")->assertOk()->assertSee($p->name);
    }

    public function test_toko_suspend_404(): void
    {
        $s = $this->store('suspended');

        $this->get("/toko/{$s->slug}")->assertNotFound();
    }

    public function test_detail_produk_menampilkan_info_toko(): void
    {
        $s = $this->store();
        $p = $this->product($s);

        $this->get("/produk/{$p->slug}")
            ->assertOk()
            ->assertSee($p->name)
            ->assertSee($s->name)
            ->assertSee($s->city);
    }
}