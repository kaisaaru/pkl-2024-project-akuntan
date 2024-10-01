<?php

namespace App\Http\Controllers;

use App\Models\Perusahaan;
use App\Models\SuratJalan;
use App\Models\OrderPenjualan;
use App\Http\Requests\StoreSuratJalanRequest;
use App\Http\Requests\UpdateSuratJalanRequest;
use App\Models\Barang;
use App\Models\detail_op;
use App\Models\detail_sj;
use App\Models\faktur_jual;
use App\Models\sj_line;
use App\Models\BukuBesar;
use App\Models\SubBukuBesar;
use App\Repository\SuratJalan\SuratJalanRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Exception;

class SuratJalanController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    protected $suratjalanRepository;
    public function __construct(SuratJalanRepository $suratjalanRepository)
    {
        $this->suratjalanRepository = $suratjalanRepository;
    }
    public function index()
    {
        $id = auth()->user()->id;
        try {
            return $this->suratjalanRepository->index($id);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function laporan()
    {
        $SuratJalan = SuratJalan::all();
        return view('barang.barangkeluar.suratjalan.dataSJ', compact('SuratJalan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }
    public function print($id_so, $id_sj)
    {
        try {
            return $this->suratjalanRepository->print($id_so, $id_sj);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
    /**
     * Store a newly created resource in storage.
     */

    public function SuratJalan()
    {
        try {
            return $this->suratjalanRepository->SuratJalan();
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
    public function store(StoreSuratJalanRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(SuratJalan $suratJalan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id)
    {
        try {
            $request->validate([
                'nama_barang' => 'required',
                'jumlah_barang' => 'required|numeric',
                // Pastikan jumlah_barang adalah angka
            ]);

            return $this->suratjalanRepository->edit($request, $id);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
        // Validasi input

    }

    public function createSuratJalan(Request $request)
    {
        try {
            $validateData = $request->validate([
                'ID_SO' => 'required',
                'tanggal_sj' => 'required|date',
                'nopol' => 'required|string',
                'nama_supir' => 'required|string',
                'ket' => 'required|string',
            ]);
            return $this->suratjalanRepository->createSuratJalan($request);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSuratJalanRequest $request, SuratJalan $suratJalan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SuratJalan $suratJalan)
    {
        //
    }

    public function status($status, $id)
    {
        try {
            return $this->suratjalanRepository->status($status, $id);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function faktur($id_sj)
    {

        try {
            return $this->suratjalanRepository->faktur($id_sj);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
