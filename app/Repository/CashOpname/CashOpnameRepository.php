<?php

namespace App\Repository\CashOpname;

use App\Models\CashOpname;
use App\Models\SubBukuBesar;
use App\Http\Requests\StoreCashOpnameRequest;
use App\Http\Requests\UpdateCashOpnameRequest;
use GuzzleHttp\Psr7\Request;

class CashOpnameRepository
{
    public function data()
    {
        try {
            $data = CashOpname::all();
            return view('opname.cash-opname', compact('data'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function update($validatedData)
    {
        try {
            // Mencari record berdasarkan barang_id
            $cashopname = CashOpname::where('pecahan', $validatedData['pecahan'])->first();

            // Jika record ditemukan, update nilainya
            if ($cashopname) {
                $cashopname->update([
                    'kertas' => $validatedData['kertas'],
                    'logam' => $validatedData['logam'],
                    'jumlah' => $validatedData['jumlah'],
                    'total' => $validatedData['total'],
                ]);
            }

            return redirect()->back();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function print()
    {
        try {
            $data = CashOpname::orderByDesc('id')->get();

            $kas = SubBukuBesar::where('ket', 'like', '%kas operasional%')->sum('jumlah');

            return view('opname.cash-opname-print', compact('data', 'kas'));
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
    public function store(StoreCashOpnameRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(CashOpname $cashOpname)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CashOpname $cashOpname)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CashOpname $cashOpname)
    {
        //
    }
}
