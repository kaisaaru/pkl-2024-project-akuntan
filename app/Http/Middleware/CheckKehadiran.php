<?php

namespace App\Http\Middleware;

use Closure;

class CheckKehadiran
{
    public function handle($request, Closure $next)
    {
        // Ambil data kehadiran dari pengguna atau dari sesi, sesuaikan dengan kebutuhan Anda
        $kehadiran = $request->user()->kehadiran;

        if ($kehadiran === 'sakit' || $kehadiran === 'izin') {
            // Jika kehadiran adalah sakit atau izin, arahkan pengguna ke halaman lain atau berikan respons yang sesuai
            return redirect('/izin-sakit-page');
        }

        // Jika kehadiran tidak sakit atau izin, lanjutkan ke route yang diminta
        return $next($request);
    }
}
