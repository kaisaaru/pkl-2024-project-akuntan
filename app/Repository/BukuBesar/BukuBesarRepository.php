<?php

namespace App\Repository\BukuBesar;

use App\Models\BukuBesar;
use App\Models\TipeAkun;
use Illuminate\Http\Request;
use App\Http\Requests\StoreBukuBesarRequest;
use App\Http\Requests\UpdateBukuBesarRequest;

class BukuBesarRepository
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $tipe = TipeAkun::all();
            $data = BukuBesar::all();
            $BukuBesar = BukuBesar::with('subBukuBesar')
                ->orderBy('no_bukubesar', 'asc')
                ->get();

            $bukubesar = BukuBesar::all();

            // Add this line to get the previous no_bukubesar
            $previousNoBukuBesar = BukuBesar::orderBy('no_bukubesar', 'desc')->value('no_bukubesar');

            return view('bukubesar.view', compact('data', 'tipe', 'BukuBesar', 'previousNoBukuBesar', 'bukubesar'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function insert(Request $request)
    {
        try {
            // Validasi data yang dikirim dari form
            $validatedData = $request->validate([
                'tipe' => 'required|string',
                'no_bukubesar' => 'required|string',
                'keterangan' => 'required|string',
            ]);

            $no = $validatedData['no_bukubesar'];
            $ket = $validatedData['keterangan'];

            // if (BukuBesar::where('no_bukubesar', $no)->exists() && BukuBesar::where('ket', $ket)->exists()) {
            //     // Jika sudah ada, kembali ke halaman sebelumnya dengan pesan kesalahan
            //     return redirect()->back()->with('error', 'No Buku Besar <strong>' . $no . '</strong> dan Keterangan Buku Besar <strong>' . $ket . '</strong> telah ada')->withInput();
            // }
            if (BukuBesar::where('ket', $ket)->exists()) {
                // Jika sudah ada, kembali ke halaman sebelumnya dengan pesan kesalahan
                return redirect()->back()->with('error', 'Keterangan Buku Besar <strong>' . $ket . '</strong> telah ada')->withInput();
                // }
            }
            if (BukuBesar::where('no_bukubesar', $no)->exists()) {
                // Jika sudah ada, kembali ke halaman sebelumnya dengan pesan kesalahan
                return redirect()->back()->with('error', 'No Buku Besar <strong>' . $no . '</strong> telah ada')->withInput();
            }

            // Simpan data kelompok ke dalam database menggunakan model
            $kelompok = new BukuBesar();
            $kelompok->tipe = $validatedData['tipe'];
            $kelompok->no_bukubesar = $validatedData['no_bukubesar'];
            $kelompok->ket = $validatedData['keterangan'];

            // Simpan data ke database
            $kelompok->save();

            // Redirect ke halaman lain atau tampilkan pesan sukses jika diperlukan
            return redirect('/bukuBesar')->with('success', 'Buku Besar <strong>' . $validatedData['keterangan'] . '</strong> berhasil ditambahkan.');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBukuBesarRequest $request)
    {
        try {
            //
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(BukuBesar $bukuBesar)
    {
        try {
            //
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $tipe = TipeAkun::all();
            $data = BukuBesar::all();
            $BukuBesar = BukuBesar::find($id);

            // Add this line to get the previous no_bukubesar

            return view('bukubesar.edit', compact('data', 'tipe', 'BukuBesar'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, Request $request)
    {
        try {

            $data = BukuBesar::find($id);
            $ketlama = $data->ket;

            $validatedData = $request->validate([
                'tipe' => 'required|string',
                'ket' => 'required|string',
            ]);

            $ket = $validatedData['ket'];

            if ($ket != $ketlama && BukuBesar::where('ket', $ket)->exists()) {
                // Jika sudah ada, kembali ke halaman sebelumnya dengan pesan kesalahan
                return redirect()->back()->with('error', 'Keterangan Buku Besar <strong>' . $ket . '</strong> telah ada')->withInput();
            }

            $data->update($validatedData);

            $keterangan = $data->ket;

            return redirect('/bukuBesar')->with('update', 'Buku Besar <strong>' . $keterangan . '</strong> berhasil diupdate.');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function destroy($id)
    {
        try {
            $data = BukuBesar::find($id);

            $data->delete();

            $kategori = $data->ket;

            return redirect('/kategori')->with('delete', 'Buku Besar <strong>' . $kategori . '</strong> berhasil dihapus.');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function cashopnem()
    {
        try {
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
