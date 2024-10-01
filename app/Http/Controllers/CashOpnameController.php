<?php

namespace App\Http\Controllers;

use App\Models\CashOpname;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCashOpnameRequest;
use App\Http\Requests\UpdateCashOpnameRequest;
use App\Repository\CashOpname\CashOpnameRepository;

class CashOpnameController extends Controller
{
    protected $cashopnameRepository;

    public function __construct(CashOpnameRepository $cashopnameRepository)
    {
        $this->cashopnameRepository = $cashopnameRepository;
    }

    public function data()
    {
        try {
            return $this->cashopnameRepository->data();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function update(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'pecahan' => 'required|integer',
                'kertas' => 'required|integer',
                'logam' => 'required|integer',
                'jumlah' => 'required|integer',
                'total' => 'required|integer',
            ]);
            return $this->cashopnameRepository->update($validatedData);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function print()
    {
        try {
            return $this->cashopnameRepository->print();
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
