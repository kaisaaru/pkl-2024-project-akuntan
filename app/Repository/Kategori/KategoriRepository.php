<?php

namespace App\Repository\Kategori;

use App\Models\Kategori;
use Illuminate\Http\Request;
use App\Http\Requests\StoreKategoriRequest;
use App\Http\Requests\UpdateKategoriRequest;

class KategoriRepository
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $data = Kategori::all();

            // if (request('search')) {
            //     $data = Kategori::where('kategori_barang', 'LIKE', '%' . request('search') . '%')->paginate(15);

            //     return view('kategori.kategori', compact('data'));
            // }

            return view('kategori.kategori', ['data' => Kategori::paginate(15)]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($validatedData)
    {
        try {           

            // Simpan data kategori ke dalam database menggunakan model
            $kategori = new Kategori();
            $kategori->kategori_barang = $validatedData['kategori_barang'];

            // Simpan data ke database
            $kategori->save();

            // Redirect ke halaman lain atau tampilkan pesan sukses jika diperlukan
            return redirect('/kategori')->with('success', 'Kategori Barang <strong>' . $validatedData['kategori_barang'] . '</strong> berhasil ditambahkan.');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function edit($id)
    {
        try {
            $data = Kategori::find($id);
            return view('kategori.edit', compact('data', 'id'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function update($validatedData, $id)
    {
        try {
            $data = Kategori::find($id);        

            $data->update($validatedData);

            $kategori = $data->kategori_barang;

            return redirect('/kategori')->with('update', 'Kategori Barang <strong>' . $kategori . '</strong> berhasil diupdate.');
            //
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $data = Kategori::find($id);

            $data->delete();

            $kategori = $data->kategori_barang;

            return redirect('/kategori')->with('delete', 'Kategori Barang <strong>' . $kategori . '</strong> berhasil dihapus.');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
