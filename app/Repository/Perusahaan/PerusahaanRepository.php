<?php

namespace App\Repository\Perusahaan;

use Illuminate\Http\Request;
use App\Models\Perusahaan;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StorePerusahaanRequest;
use App\Http\Requests\UpdatePerusahaanRequest;

class PerusahaanRepository
{
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

    public function create()
    {
        return view('relasi.create');
    }

    public function store($validatedData)
    {
        try {
            // Validasi data yang dikirim dari form
            Perusahaan::create($validatedData);


            // Redirect ke halaman lain atau tampilkan pesan sukses jika diperlukan
            return redirect('/app/relasi')->with('success', 'Perusahaan <strong>' . $validatedData['nama_perusahaan'] . '</strong> berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $data = Perusahaan::find($id);
            return view('relasi.edit', compact('data'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function update($id, Request $request)
    {
        try {
            $data = Perusahaan::find($id);
            $perusahaan = $data->nama_perusahaan;

            if ($data->jenis == 'Developer') {
                // Validasi data yang dikirim dari form
                $validatedData = $request->validate([
                    'nama_perusahaan' => 'required',
                    'alamat_kantor' => 'required',
                    'alamat_gudang' => 'required',
                    'nama_pimpinan' => 'required',
                    'no_telepon' => 'required',
                    // 'plafon_debit' => 'required',
                ]);

                // Menambah field updated_at dengan waktu sekarang + 7 jam
                $validatedData['updated_at'] = now()->addHours(7);

                $data->update($validatedData);
            } else if ($data->jenis == 'Supplier') {
                // Validasi data yang dikirim dari form
                $validatedData = $request->validate([
                    'nama_perusahaan' => 'required',
                    'alamat_kantor' => 'required',
                    'alamat_gudang' => 'required',
                    'nama_pimpinan' => 'required',
                    'no_telepon' => 'required',
                    // 'plafon_debit' => 'required',
                ]);

                // Menambah field updated_at dengan waktu sekarang + 7 jam
                $validatedData['updated_at'] = now()->addHours(7);

                $data->update($validatedData);
            } else if ($data->jenis == 'Konsumen') {
                // Validasi data yang dikirim dari form
                $validatedData = $request->validate([
                    'nama_perusahaan' => 'required',
                    'alamat_kantor' => 'required',
                    'alamat_gudang' => 'required',
                    'nama_pimpinan' => 'required',
                    'no_telepon' => 'required',
                    // 'plafon_kredit' => 'required',
                ]);

                // Menambah field updated_at dengan waktu sekarang + 7 jam
                $validatedData['updated_at'] = now()->addHours(7);

                $data->update($validatedData);
            }

            return redirect('/app/relasi')->with('update', 'Perusahaan <strong>' . $perusahaan . '</strong> berhasil diupdate.');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }


    public function destroy($id)
    {
        try {
            $data = Perusahaan::find($id);
            $perusahaan = $data->nama_perusahaan;
            $data->delete();
            return redirect('/app/relasi')->with('delete', 'Perusahaan <strong>' . $perusahaan . '</strong> berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function profile()
    {
        return view('relasi.profile');
    }

    public function profileedit()
    {
        return view('relasi.edit-profile');
    }
}
