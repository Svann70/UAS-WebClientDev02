<?php

use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MerchantStoreTest extends TestCase
{
    use RefreshDatabase;

    private function merchant(string $suffix = ''): User
    {
        $u = User::create(['name' => 'M' . $suffix, 'email' => 'm' . $suffix . '@test.id', 'password' => 'rahasia123']);
        $u->role = 'merchant';
        $u->save();

        return $u;
    }

    private function payload(array $over = []): array
    {
        return array_merge([
            'name' => 'Dapur Bu Sari',
            'description' => 'Camilan rumahan khas Sunda',
            'city' => 'Bandung',
            'address' => 'Jl. Merdeka No. 10',
        ], $over);
    }

    public function test_merchant_bisa_membuat_toko_pertama(): void
    {
        $m = $this->merchant();

        $this->actingAs($m)->put('/merchant/store', $this->payload())
            ->assertRedirect(route('merchant.store.edit'));

        $this->assertDatabaseHas('stores', [
            'user_id' => $m->id,
            'name' => 'Dapur Bu Sari',
            'slug' => 'dapur-bu-sari',
            'status' => 'active',
        ]);
    }

    public function test_user_id_tidak_bisa_dipalsukan(): void
    {
        $m = $this->merchant();
        $lain = $this->merchant('lain');
        $lain->update(['email' => 'lain2@test.id']);

        $this->actingAs($m)->put('/merchant/store', $this->payload(['user_id' => $lain->id]));

        $this->assertDatabaseHas('stores', ['user_id' => $m->id]);
        $this->assertDatabaseMissing('stores', ['user_id' => $lain->id]);
    }

    public function test_status_tidak_bisa_diubah_dari_form(): void
    {
        $m = $this->merchant();

        $this->actingAs($m)->put('/merchant/store', $this->payload(['status' => 'suspended']));

        // Status tetap default, bukan nilai kiriman form.
        $this->assertDatabaseHas('stores', ['user_id' => $m->id, 'status' => 'active']);
    }

    public function test_merchant_bisa_update_toko_dan_slug_ikut_berubah(): void
    {
        $m = $this->merchant();
        $this->actingAs($m)->put('/merchant/store', $this->payload());

        $this->actingAs($m)->put('/merchant/store', $this->payload(['name' => 'Dapur Baru Sari']))
            ->assertRedirect(route('merchant.store.edit'));

        $this->assertDatabaseHas('stores', ['user_id' => $m->id, 'name' => 'Dapur Baru Sari', 'slug' => 'dapur-baru-sari']);
    }

    public function test_slug_toko_bentrok_dapat_suffix(): void
    {
        $a = $this->merchant();
        $this->actingAs($a)->put('/merchant/store', $this->payload());

        $b = User::create(['name' => 'M2', 'email' => 'm2@test.id', 'password' => 'rahasia123']);
        $b->role = 'merchant';
        $b->save();
        $this->actingAs($b)->put('/merchant/store', $this->payload());

        $this->assertDatabaseHas('stores', ['user_id' => $a->id, 'slug' => 'dapur-bu-sari']);
        $this->assertDatabaseHas('stores', ['user_id' => $b->id, 'slug' => 'dapur-bu-sari-2']);
    }

    public function test_logo_upload_tersimpan_dengan_nama_acak(): void
    {
        Storage::fake('public');
        $m = $this->merchant();

        $this->actingAs($m)->put('/merchant/store', $this->payload([
            'logo' => UploadedFile::fake()->create('logo toko.png', 100, 'image/png'),
        ]));

        $store = Store::where('user_id', $m->id)->firstOrFail();
        $this->assertNotNull($store->logo);
        $this->assertStringNotContainsString('logo toko', $store->logo);
        Storage::disk('public')->assertExists($store->logo);
    }

    public function test_logo_lama_dihapus_saat_diganti(): void
    {
        Storage::fake('public');
        $m = $this->merchant();

        $this->actingAs($m)->put('/merchant/store', $this->payload([
            'logo' => UploadedFile::fake()->create('lama.jpg', 100, 'image/jpeg'),
        ]));
        $store = Store::where('user_id', $m->id)->firstOrFail();
        $old = $store->logo;

        $this->actingAs($m)->put('/merchant/store', $this->payload([
            'logo' => UploadedFile::fake()->create('baru.jpg', 100, 'image/jpeg'),
        ]));

        Storage::disk('public')->assertMissing($old);
        $this->assertNotSame($old, $store->fresh()->logo);
    }

    public function test_field_wajib_divalidasi(): void
    {
        $m = $this->merchant();

        $this->actingAs($m)->put('/merchant/store', ['name' => '', 'city' => '', 'address' => ''])
            ->assertSessionHasErrors(['name', 'city', 'address']);
    }

    public function test_customer_tidak_bisa_akses_profil_toko(): void
    {
        $c = User::create(['name' => 'C', 'email' => 'c@test.id', 'password' => 'rahasia123']);
        $c->role = 'customer';
        $c->save();

        $this->actingAs($c)->get('/merchant/store')->assertForbidden();
    }
}