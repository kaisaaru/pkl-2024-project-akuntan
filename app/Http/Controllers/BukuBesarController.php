<?php

namespace App\Http\Controllers;

use App\Models\BukuBesar;
use App\Models\TipeAkun;
use Illuminate\Http\Request;
use App\Http\Requests\StoreBukuBesarRequest;
use App\Http\Requests\UpdateBukuBesarRequest;
use App\Repository\BukuBesar\BukuBesarRepository;

class BukuBesarController extends Controller
{
    protected $bukubesarRepository;

    public function __construct(BukuBesarRepository $bukubesarRepository)
    {
        $this->bukubesarRepository = $bukubesarRepository;
    }

    public function index()
    {
        try {
            return $this->bukubesarRepository->index();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function insert(Request $request)
    {
        try {
            return $this->bukubesarRepository->insert($request);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function store(StoreBukuBesarRequest $request)
    {
        try {
            // Lakukan operasi yang diperlukan di sini, jika ada
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function show(BukuBesar $bukuBesar)
    {
        try {
            // Lakukan operasi yang diperlukan di sini, jika ada
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function edit($id)
    {
        try {
            return $this->bukubesarRepository->edit($id);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function update($id, Request $request)
    {
        try {
            return $this->bukubesarRepository->update($id, $request);            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function destroy($id)
    {
        try {
            return $this->bukubesarRepository->destroy($id);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function cashopnem()
    {
        try {
            return $this->bukubesarRepository->cashopnem();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
