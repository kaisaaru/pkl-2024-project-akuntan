<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use App\Http\Requests\StoreKategoriRequest;
use App\Http\Requests\UpdateKategoriRequest;
use App\Repository\Kategori\KategoriRepository;

class KategoriController extends Controller
{
    protected $kategoriRepository;

    public function __construct(KategoriRepository $kategoriRepository)
    {
        $this->kategoriRepository = $kategoriRepository;
    }

    public function index()
    {
        try {
            return $this->kategoriRepository->index();            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'kategori_barang' => 'required|string',
        ]);
        try {
            return $this->kategoriRepository->store($validatedData);            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function edit($id)
    {
        try {
            return $this->kategoriRepository->edit($id);            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function update($id, Request $request)
    {
        $validatedData = $request->validate([
            'kategori_barang' => 'required|unique:kategoris,kategori_barang',
        ]);
        try {
            return $this->kategoriRepository->update($validatedData, $id);            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function destroy($id)
    {
        try {
            return $this->kategoriRepository->destroy($id);            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
