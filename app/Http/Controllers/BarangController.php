<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Http\Requests\StoreBarangRequest;
use App\Http\Requests\UpdateBarangRequest;


class BarangController extends Controller

{
    /**
     * Display a listing of the resource.
     */

    public function index($ids)
    {
        try {
            // $userBarang = Barang::where('user_id', $ids)->paginate(15);
            $userBarang = Barang::paginate(15);
            // Retrieving these variables once is sufficient
            $kategori = Kategori::all();
            $kelompokOptions = Kelompok::all();
            $perusahaan = Perusahaan::all();

            return view('barang.barang', ['data' => $userBarang], compact('kelompokOptions', 'kategori', 'perusahaan'));
        } catch (\Exception $e) {
            // If this is a web route, you might want to redirect with an error message
            return response()->json(['error' => $e->getMessage()], 400);
        }

    public function index()
    {
        //

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
    public function store(StoreBarangRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Barang $barang)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Barang $barang)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBarangRequest $request, Barang $barang)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Barang $barang)
    {
        //
    }
}
