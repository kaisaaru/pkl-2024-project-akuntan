<?php

namespace App\Http\Controllers;

use App\Models\Perusahaan;
use Illuminate\Http\Request;
// use App\Http\Requests\StorePerusahaanRequest;
// use App\Http\Requests\UpdatePerusahaanRequest;
use Illuminate\Support\Facades\Auth;

class PerusahaanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Mendapatkan kode_perusahaan dari user yang sedang login
            $userKodePerusahaan = Auth::user()->kode_perusahaan;
            $developer = 'Developer';

            // Mendapatkan data perusahaan, tetapi hanya yang tidak sesuai dengan kode_perusahaan user
            $perusahaanData = Perusahaan::where('jenis', '!=', $developer)->paginate(15);
            return view('relasi.perusahaan', ['data' => $perusahaanData]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Perusahaan $perusahaan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Perusahaan $perusahaan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($request, Perusahaan $perusahaan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Perusahaan $perusahaan)
    {
        //
    }
}
