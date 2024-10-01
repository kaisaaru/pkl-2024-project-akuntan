<?php

namespace App\Http\Controllers;

use App\Models\SuratJalan;
use App\Http\Requests\StoreSuratJalanRequest;
use App\Repository\Jurnal\JurnalRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Exception;

class JurnalController extends Controller
{
    protected $jurnalRepository;

    public function __construct(JurnalRepository $jurnalRepository)
    {
        $this->jurnalRepository = $jurnalRepository;
    }

    public function jurnal($id_sj)
    {
        try {
            return $this->jurnalRepository->jurnal($id_sj);
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan.');
        }
    }

    public function create(Request $request, $id_sj)
    {
        // dd($request);
        $validatedData = $request->validate([
            'tanggal' => 'required',
            'id_sj' => 'required',
            'ketsj' => 'required',
            "no_subbukubesar.*" => 'required',
            "kredit.*" => 'required',
            "debit.*" => 'required',
            "ket.*" => 'required'
        ]);

        try {
            return $this->jurnalRepository->create($validatedData, $id_sj);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function inputlain(Request $request)
    {
        try {
            return $this->jurnalRepository->inputlain($request);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function jurnallain(Request $request)
    {
        $validatedData = $request->validate([
            'tanggal' => 'required',
            'ketsj' => 'required',
            "no_subbukubesar.*" => 'required',
            "kredit.*" => 'required',
            "debit.*" => 'required',
            "ket.*" => 'required'
        ]);

        try {
            return $this->jurnalRepository->jurnallain($validatedData);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
