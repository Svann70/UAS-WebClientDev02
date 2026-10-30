<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware RBAC: membatasi akses route berdasarkan kolom `role` pada user.
 *
 * Kenapa middleware, bukan pengecekan `if` di dalam controller?
 * 1. Satu tempat untuk semua pengecekan izin -> tidak mungkin ada route yang lupa dicek.
 * 2. Request ditolak SEBELUM controller jalan -> query database tidak terbuang.
 * 3. Route group di routes/web.php jadi deklaratif: `middleware('role:admin')`.
 */
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // Belum login -> biar middleware `auth` yang urus, tapi aman kalau tidak dipasang.
        if (! $user) {
            abort(403, 'Silakan login terlebih dahulu.');
        }

        // Role tidak cocok -> 403 Forbidden, bukan 404,
        // supaya jelas "kamu tidak punya izin", bukan "halaman tidak ada".
        if (! in_array($user->role, $roles, true)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}