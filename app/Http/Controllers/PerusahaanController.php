<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Perusahaan;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StorePerusahaanRequest;
use App\Http\Requests\UpdatePerusahaanRequest;
use App\Repository\Perusahaan\PerusahaanRepository;

class PerusahaanController extends Controller
{
    protected $perusahaanRepository;

    public function __construct(PerusahaanRepository $perusahaanRepository)
    {
        $this->perusahaanRepository = $perusahaanRepository;
    }

    public function index()
    {
        try {
           return $this->perusahaanRepository->index();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function create()
    {
        return view('relasi.create');
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'nama_perusahaan' => 'required',
                'jenis' => 'required',
                'alamat_kantor' => 'required',
                'alamat_gudang' => 'required',
                'nama_pimpinan' => 'required',
                'no_telepon' => 'required',
                'plafon_debit' => 'nullable',
                'plafon_kredit' => 'nullable',
            ]);

            return $this->perusahaanRepository->store($validatedData);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            // $data = $this->perusahaanRepository->edit($id);
            return $this->perusahaanRepository->edit($id);
            // return view('relasi.edit', compact('data'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function update($id, Request $request)
    {
        try {
            return $this->perusahaanRepository->update($id, $request);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function destroy($id)
    {
        try {
            return $this->perusahaanRepository->destroy($id);
        } catch (\Exception $e) {
            return redirect('/relasi')->with('error', 'Error deleting perusahaan.');
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
