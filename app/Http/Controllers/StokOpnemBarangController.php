<?php

namespace App\Http\Controllers;

use App\Models\StokOpnemBarang;
use App\Models\Barang;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Requests\StoreStokOpnemBarangRequest;
use App\Http\Requests\UpdateStokOpnemBarangRequest;
use App\Repository\StokOpnemBarang\StokOpnemBarangRepository;

class StokOpnemBarangController extends Controller
{
    protected $stokopnembarangRepository;

    public function __construct(StokOpnemBarangRepository $stokopnembarangRepository)
    {
        $this->stokopnembarangRepository = $stokopnembarangRepository;
    }

    public function index($barang_id)
    {
        try {
            return $this->stokopnembarangRepository->index($barang_id);          
        } catch (\Exception $e) {
            return redirect()->route('your.error.route')->with('error', 'An error occurred.');
        }
    }

    public function print($barang_id, Request $request)
    {
        try {
            return $this->stokopnembarangRepository->print($barang_id, $request);         
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function store(StoreStokOpnemBarangRequest $request)
    {
        // Your implementation here if needed
    }

    public function show(StokOpnemBarang $stokOpnemBarang)
    {
        // Your implementation here if needed
    }

    public function edit(StokOpnemBarang $stokOpnemBarang)
    {
        // Your implementation here if needed
    }

    public function update(UpdateStokOpnemBarangRequest $request, StokOpnemBarang $stokOpnemBarang)
    {
        // Your implementation here if needed
    }

    public function destroy(StokOpnemBarang $stokOpnemBarang)
    {
        // Your implementation here if needed
    }
}
