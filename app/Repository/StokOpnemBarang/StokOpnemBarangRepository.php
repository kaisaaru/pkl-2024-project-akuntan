<?php

namespace App\Repository\StokOpnemBarang;

use App\Models\StokOpnemBarang;
use App\Models\Barang;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Requests\StoreStokOpnemBarangRequest;
use App\Http\Requests\UpdateStokOpnemBarangRequest;

class StokOpnemBarangRepository
{
    /**
     * Display a listing of the resource.
     */
    public function index($barang_id)
    {
        try {
            $so = StokOpnemBarang::where('kode_barang', $barang_id)->get();

            $barang = Barang::where('barang_id', $barang_id)->first();

            return view('stok-opnem.barang.barang', compact('so', 'barang_id', 'barang'));
        } catch (\Exception $e) {
            // If this is a web route, you might want to redirect with an error message
            return redirect()->route('your.error.route')->with('error', 'An error occurred.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function print($barang_id, Request $request)
    {
        try {
            // Convert to Carbon instances
            $awal = Carbon::parse($request->input('awal'));
            $akhir = Carbon::parse($request->input('akhir'));

            $barang = Barang::where('barang_id', $barang_id)->first();

            if ($awal && $akhir) {
                $data = StokOpnemBarang::where('kode_barang', $barang_id)
                    ->whereBetween('tanggal', [$awal, $akhir])
                    ->orderBy('tanggal')
                    ->join('perusahaans', 'stok_opnem_barangs.ket', '=', 'perusahaans.kode_perusahaan')
                    ->select('stok_opnem_barangs.*', 'perusahaans.nama_perusahaan')
                    ->get();


                $stokawal = StokOpnemBarang::where('kode_barang', $barang_id)
                    ->where('tanggal', '<', $awal)
                    ->orderBy('tanggal', 'desc')
                    ->first();

                $stokawal = $stokawal ? $stokawal->stok : 0;

                $stokakhir = $data->isEmpty() ? $stokawal : $data->last()->stok;

                if ($data->isNotEmpty()) {
                    return view('stok-opnem.barang.printwithtgl', compact('data', 'barang_id', 'awal', 'akhir', 'stokawal', 'stokakhir', 'barang'));
                } else {
                    $data = StokOpnemBarang::where('kode_barang', $barang_id)
                        ->orderBy('tanggal')
                        ->get();

                    $stokawal = 0;

                    $stokakhir = $data->isEmpty() ? $stokawal : $data->last()->stok;

                    if ($data->isNotEmpty()) {
                        return view('stok-opnem.barang.print', compact('data', 'barang_id', 'stokawal', 'stokakhir', 'barang'));
                    } else {
                        // Handle the case where no records are found, for example, redirect or display an error message
                        return view('stok-opnem.barang.print', compact('data', 'barang_id', 'stokawal', 'stokakhir', 'barang'));
                    }
                }
            }
        } catch (\Exception $e) {
            // If this is a web route, you might want to redirect with an error message
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStokOpnemBarangRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(StokOpnemBarang $stokOpnemBarang)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StokOpnemBarang $stokOpnemBarang)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStokOpnemBarangRequest $request, StokOpnemBarang $stokOpnemBarang)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StokOpnemBarang $stokOpnemBarang)
    {
        //
    }
}
