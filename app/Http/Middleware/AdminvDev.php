<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminvDev
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(!auth()->check() || auth()->user()->kategori ==! 'Admin' || auth()->user()->kategori ===! 'Developer'){
            return redirect('/home')->with('error', 'Anda tidak ada akses untuk kesana!');
        }
        return $next($request);
    }
}
