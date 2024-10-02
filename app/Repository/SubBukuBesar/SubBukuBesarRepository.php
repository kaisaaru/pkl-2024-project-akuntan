<?php

namespace App\Repository\SubBukuBesar;

use App\Models\SubBukuBesar;
use App\Models\BukuBesar;
use Illuminate\Http\Request;
use App\Http\Requests\StoreSubBukuBesarRequest;
use App\Http\Requests\UpdateSubBukuBesarRequest;

class SubBukuBesarRepository
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        if ($id === null) {
            return redirect('/subbukuBesar')->with('error', 'Terjadi Kesalahan');
        }
        try {
            $data = SubBukuBesar::all();
            $bukubesar = BukuBesar::all();

            // Add this line to get the previous no_bukubesar
            $previousNoSubBukuBesar = SubBukuBesar::orderBy('no_subbukubesar', 'desc')->value('no_subbukubesar');

            return view('bukubesar.sub.view', compact('data', 'bukubesar', 'previousNoSubBukuBesar'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error retrieving data.');
        }
    }

    public function insert(Request $request)
    {
        try {
            // Validasi data yang dikirim dari form
            $validatedData = $request->validate([
                'no_bukubesar' => 'required|string',
                'no_subbukubesar' => 'required|string',
                'ket' => 'required|string',
            ]);

            // Simpan data kelompok ke dalam database menggunakan model
            $kelompok = new SubBukuBesar();
            $kelompok->no_bukubesar = $validatedData['no_bukubesar'];
            $kelompok->no_subbukubesar = $validatedData['no_subbukubesar'];
            $kelompok->ket = $validatedData['ket'];

            // Simpan data ke database
            $kelompok->save();

            // Redirect ke halaman lain atau tampilkan pesan sukses jika diperlukan
            return redirect()->back()->with('success', 'Sub Buku Besar <strong>' . $validatedData['ket'] . '</strong> berhasil ditambahkan.');
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
            $data = BukuBesar::all();
            $SubBukuBesar = SubBukuBesar::find($id);
            $no = $SubBukuBesar->no_bukubesar;
            $BukuBesar = BukuBesar::where('no_bukubesar', $no)->first();
            $ket = $BukuBesar->ket;
            // Add this line to get the previous no_bukubesar

            return view('bukubesar.sub.edit', compact('data', 'SubBukuBesar', 'BukuBesar', 'ket'));
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

            $data = SubBukuBesar::find($id);

            $validatedData = $request->validate([
                'ket' => 'required|string',
            ]);

            $ket = $validatedData['ket'];

            $data->update($validatedData);

            $keterangan = $data->ket;

            return redirect()->back()->with('update', 'Sub Buku Besar <strong>' . $keterangan . '</strong> berhasil diupdate.');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubBukuBesar $subBukuBesar)
    {
        //
    }
}
