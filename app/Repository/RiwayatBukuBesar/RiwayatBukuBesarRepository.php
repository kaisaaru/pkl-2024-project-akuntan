<?php

namespace App\Repository\RiwayatBukuBesar;

use App\Models\BukuBesar;
use App\Models\RiwayatBukuBesar;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Requests\StoreRiwayatBukuBesarRequest;
use App\Http\Requests\UpdateRiwayatBukuBesarRequest;
use App\Models\SubBukuBesar;

class RiwayatBukuBesarRepository
{
    /**
     * Display a listing of the resource.
     */
    public function index($no_subbukubesar, Request $request)
    {
        try {
            // Convert to Carbon instances
            $awal = Carbon::parse($request->input('awal'));
            $akhir = Carbon::parse($request->input('akhir'));

            $bukubesar = SubBukuBesar::where('no_subbukubesar', $no_subbukubesar)->first();
            // Check if both start and end dates are provided
            if ($awal && $akhir) {
                // Fetch data within the specified date range
                $data = RiwayatBukuBesar::where('no_subbukubesar', $no_subbukubesar)
                    ->whereBetween('tanggal', [$awal, $akhir])
                    ->orderBy('tanggal')
                    ->get();

                $saldoawal = RiwayatBukuBesar::where('no_subbukubesar', $no_subbukubesar)
                    ->where('tanggal', '<', $awal)
                    ->orderBy('tanggal', 'desc')
                    ->first();

                $saldoawal = $saldoawal ? $saldoawal->saldo_kumulatif : 0;

                // Calculate the final balance based on the last record in the fetched data
                $saldoakhir = $data->isEmpty() ? $saldoawal : $data->last()->saldo_kumulatif;
                // Check if data is not empty before proceeding
                if ($data->isNotEmpty()) {
                    return view('bukubesar.printwithtgl', compact('data', 'no_subbukubesar', 'awal', 'akhir', 'saldoawal', 'saldoakhir', 'bukubesar'));
                } else {
                    $data = RiwayatBukuBesar::where('no_subbukubesar', $no_subbukubesar)
                        ->orderBy('tanggal')
                        ->get(); // Use get() instead of first()

                    // Calculate the initial balance based on all data
                    $saldoawal = 0;

                    // Calculate the final balance based on the last record in the fetched data
                    $saldoakhir = $data->isEmpty() ? $saldoawal : $data->last()->saldo_kumulatif;
                    // Check if data is not empty before proceeding
                    if ($data->isNotEmpty()) {
                        return view('bukubesar.print', compact('data', 'no_subbukubesar', 'saldoawal', 'saldoakhir', 'bukubesar'));
                    } else {
                        // Handle the case where no records are found, for example, redirect or display an error message
                        return view('bukubesar.print', compact('data', 'no_subbukubesar', 'saldoawal', 'saldoakhir', 'bukubesar'));
                    }
                }
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRiwayatBukuBesarRequest $request)
    {
        try {
            //
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error storing data.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(RiwayatBukuBesar $riwayatBukuBesar)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RiwayatBukuBesar $riwayatBukuBesar)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRiwayatBukuBesarRequest $request, RiwayatBukuBesar $riwayatBukuBesar)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RiwayatBukuBesar $riwayatBukuBesar)
    {
        //
    }
}
