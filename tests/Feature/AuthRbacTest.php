<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthRbacTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $role): User
    {
        $u = User::create([
            'name' => ucfirst($role),
            'email' => $role . '@test.id',
            'password' => 'rahasia123',
        ]);
        $u->role = $role;
        $u->save();

        return $u;
    }

    public function test_customer_tidak_bisa_akses_admin_dashboard(): void
    {
        $this->actingAs($this->makeUser('customer'))
            ->get('/admin/dashboard')
            ->assertForbidden();
    }

    public function test_merchant_tidak_bisa_akses_admin_dashboard(): void
    {
        $this->actingAs($this->makeUser('merchant'))
            ->get('/admin/dashboard')
            ->assertForbidden();
    }

    public function test_admin_bisa_akses_admin_dashboard(): void
    {
        $this->actingAs($this->makeUser('admin'))
            ->get('/admin/dashboard')
            ->assertOk();
    }

    public function test_guest_diarahkan_ke_login(): void
    {
        $this->get('/merchant/dashboard')->assertRedirect('/login');
    }

    public function test_registrasi_tidak_boleh_pilih_role_admin(): void
    {
        $this->post('/register', [
            'name' => 'Nakal',
            'email' => 'nakal@test.id',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
            'role' => 'admin',
        ])->assertSessionHasErrors('role');

        $this->assertDatabaseMissing('users', ['email' => 'nakal@test.id']);
    }

    public function test_registrasi_merchant_berhasil_dan_terarah_ke_dashboard(): void
    {
        $this->post('/register', [
            'name' => 'Bu Sari',
            'email' => 'sari@test.id',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
            'role' => 'merchant',
        ])->assertRedirect(route('merchant.dashboard'));

        $this->assertDatabaseHas('users', ['email' => 'sari@test.id', 'role' => 'merchant']);
    }

    public function test_password_tidak_disimpan_plaintext(): void
    {
        $this->post('/register', [
            'name' => 'Tes Hash',
            'email' => 'hash@test.id',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
            'role' => 'customer',
        ]);

        $u = User::where('email', 'hash@test.id')->firstOrFail();
        $this->assertNotSame('rahasia123', $u->password);
        $this->assertTrue(password_verify('rahasia123', $u->password));
    }

    public function test_login_password_salah_ditolak(): void
    {
        $this->makeUser('customer');

        $this->post('/login', ['email' => 'customer@test.id', 'password' => 'salah'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}