<?php

namespace App\Repository\Barang;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Kelompok;
use App\Models\Perusahaan;
use App\Models\PurchaseOrder;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BarangRepository
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
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $kategori = Kategori::all();
            $kelompok = Kelompok::all();
            $perusahaan = Perusahaan::all();

            $kelompokOptions = Kelompok::join('kategoris', 'kelompoks.kode_kategori', '=', 'kategoris.kode_kategori')
                ->select('kelompoks.kode_kelompok', 'kelompoks.kelompok_barang', 'kategoris.kode_kategori', 'kategoris.kategori_barang')
                ->get();

            $data = Barang::paginate(15);
            return view('barang.barang', compact('data', 'kategori', 'kelompok', 'perusahaan', 'kelompokOptions'));

            // return view('barang.create', compact('kategori', 'kelompok', 'perusahaan', 'kelompokOptions'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi data yang dikirim dari form
        $validatedData = $request->validate([
            'nama_barang' => 'required|string',
            'satuan' => 'required|string',
            'kategori' => 'required|string',
            'kelompok' => 'required|string',
            'harga_beli' => 'required|numeric',
            'perusahaan' => 'required|string',
        ]);

        try {
            $stokDefault = 0;
            $hargaJualDefault = $validatedData['harga_beli'] * 1.1; // Harga jual = 110% dari harga beli
            $id = auth()->user();
            $ids = $id->id;
            // Simpan data barang ke dalam database menggunakan model
            Barang::create([
                'nama_barang' => $validatedData['nama_barang'],
                'user_id' => $ids,
                'satuan' => $validatedData['satuan'],
                'kategori' => $validatedData['kategori'],
                'kelompok' => $validatedData['kelompok'],
                'harga_beli' => $validatedData['harga_beli'],
                'Perusahaan' => $validatedData['perusahaan'],
                'stok' => $stokDefault,
                'harga_jual' => $hargaJualDefault,
            ]);
            // Redirect ke halaman lain atau tampilkan pesan sukses jika diperlukan
            return redirect('/barang')->with('success', 'Barang berhasil ditambahkan.');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data = Barang::find($id);

        $kategori = Kategori::all();
        $kelompok = Kelompok::all();
        $perusahaan = Perusahaan::all();

        $kelompokOptions = Kelompok::join('kategoris', 'kelompoks.kode_kategori', '=', 'kategoris.kode_kategori')
            ->select('kelompoks.kode_kelompok', 'kelompoks.kelompok_barang', 'kategoris.kode_kategori', 'kategoris.kategori_barang')
            ->get();

        return view('barang.edit', compact('data', 'kategori', 'kelompok', 'perusahaan', 'kelompokOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, $validatedData)
    {
        $data = Barang::find($id);

        // Validasi data yang dikirim dari form
        // Calculate harga_jual as 110% of harga_beli
        $validatedData['harga_jual'] = $validatedData['harga_beli'] * 1.1;

        $barang = $data->nama_barang;
        $data->update($validatedData);

        return redirect('/barang')->with('update', 'Barang <strong>' . $barang . '</strong> telah diupdate');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $data = Barang::find($id);
            $barang = $data->nama_barang;
            $data->delete();
            return redirect('/barang')->with('delete', 'Barang <strong>' . $barang . '</strong> telah dihapus');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function print()
    {
        try {
            $kategori = Kategori::with('barang')->get();
            // $data = Barang::all();

            return view('barang.print', compact('kategori'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function sobarangmanual()
    {
        try {
            $barang = Barang::all();
            return view('barang.so-barang', compact('barang'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function sobarangupdate($validatedData)
    {
        try {
            // Mencari record berdasarkan barang_id
            $barang = Barang::where('barang_id', $validatedData['barang_id'])->first();

            // Jika record ditemukan, update nilainya
            if ($barang) {
                $barang->update([
                    'phisik' => $validatedData['phisik'],
                    'ket' => $validatedData['ket'],
                    'selisih' => $validatedData['sistem'] - $validatedData['phisik'],
                ]);
            }

            return redirect()->back();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
