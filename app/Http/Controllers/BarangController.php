<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Kelompok;
use App\Models\Perusahaan;
use App\Models\PurchaseOrder;
use App\Models\User;
use App\Repository\Barang\BarangRepository;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BarangController extends Controller
{
    protected $barangRepository;

    public function __construct(BarangRepository $barangRepository)
    {
        $this->barangRepository = $barangRepository;
    }

    public function index()
    {
        try {
            $id = auth()->user();
            $ids = $id->id;
            return $this->barangRepository->index($ids);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function create()
    {
        try {
            return $this->barangRepository->create();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama_barang' => 'required|string',
            'satuan' => 'required|string',
            'kategori' => 'required|string',
            'kelompok' => 'required|string',
            'harga_beli' => 'required|numeric',
            'perusahaan' => 'required|string',
        ]);

        try {
            return $this->barangRepository->store($request);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function edit($id)
    {
        try {
            return $this->barangRepository->edit($id);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function update($id, Request $request)
    {
        $validatedData = $request->validate([
            'nama_barang' => 'required|string',
            'satuan' => 'required|string',
            'kategori' => 'required|string',
            'kelompok' => 'required|string',
            'harga_beli' => 'required|numeric',
            'perusahaan' => 'required|string',
        ]);

        try {
            return $this->barangRepository->update($id, $validatedData);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function destroy($id)
    {
        try {
            return $this->barangRepository->destroy($id);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function print()
    {
        try {
            return $this->barangRepository->print();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function sobarangmanual()
    {
        try {
            return $this->barangRepository->sobarangmanual();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function sobarangupdate(Request $request)
    {
        $validatedData = $request->validate([
            'barang_id' => 'required|string',
            'sistem' => 'required|string',
            'phisik' => 'required|integer',
            'ket' => 'required|string',
        ]);
        try {
            return $this->barangRepository->sobarangupdate($validatedData);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
