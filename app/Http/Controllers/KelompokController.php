<?php

namespace App\Http\Controllers;

use App\Models\Kelompok;
use App\Models\Kategori;
use Illuminate\Http\Request;
use App\Http\Requests\StoreKelompokRequest;
use App\Http\Requests\UpdateKelompokRequest;
use App\Repository\Kelompok\KelompokRepository;

class KelompokController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     protected $kelompokRepository;
    public function __construct(KelompokRepository $kelompokRepository)
    {
        $this->kelompokRepository = $kelompokRepository;
    }
    public function index()
    {
        try {
            $kelompokData = Kelompok::paginate(15);
            $kategoriData = Kategori::all();

            return view('kelompok.kelompok', compact('kelompokData', 'kategoriData'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'kode_kategori' => 'required|string',
                'kelompok_barang' => 'required|string',
            ]);
            // Simpan data kelompok ke dalam database menggunakan model
            $kelompok = new Kelompok();
            $kelompok->kode_kategori = $validatedData['kode_kategori'];
            $kelompok->kelompok_barang = $validatedData['kelompok_barang'];

            // Simpan data ke database
            $kelompok->save();

            // Redirect ke halaman lain atau tampilkan pesan sukses jika diperlukan
            return redirect('/kelompok')->with('success', 'Kategori Barang <strong>' . $validatedData['kelompok_barang'] . '</strong> berhasil ditambahkan.');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function edit($id)
    {
        try {
            $kelompokData = Kelompok::find($id);
            $kategoriData = Kategori::all();
            return view('kelompok.edit', compact('kelompokData', 'kategoriData'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function update($id, Request $request)
    {
        try {
            $data = Kelompok::find($id);

            // Validasi data yang dikirim dari form
            $validatedData = $request->validate([
                'kode_kategori' => 'required|string',
                'kelompok_barang' => 'required|string',
            ]);

            $data->update($validatedData);
            $kelompok = $data->kelompok_barang;

            return redirect('/kelompok')->with('update', 'Kategori Barang <strong>' . $kelompok . '</strong> berhasil diupdate.');
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
            $data = Kelompok::find($id);
            $data->delete();
            $kelompok = $data->kelompok_barang;
            return redirect('/kelompok')->with('delete', 'Kategori Barang <strong>' . $kelompok . '</strong> berhasil dihapus.');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
