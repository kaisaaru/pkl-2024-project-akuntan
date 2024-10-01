<?php

namespace App\Http\Controllers;

use App\Models\SubBukuBesar;
use App\Models\BukuBesar;
use Illuminate\Http\Request;
use App\Http\Requests\StoreSubBukuBesarRequest;
use App\Http\Requests\UpdateSubBukuBesarRequest;
use App\Repository\SubBukuBesar\SubBukuBesarRepository;

class SubBukuBesarController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    protected $subbukubesarRepository;

    public function __construct(SubBukuBesarRepository $subbukubesarRepository)
    {
        $this->subbukubesarRepository = $subbukubesarRepository;
    }
    public function index()
    {
        $id = auth()->user()->id;
        try {
            return $this->subbukubesarRepository->index($id);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error retrieving data.');
        }
    }

    public function insert(Request $request)
    {
        try {
            // Validasi data yang dikirim dari form
            $validatedData = $request->validate([
                'no_bukubesar' => 'required|string',
                'no_subbukubesar' => 'required|string',
                'ket' => 'required|string',
            ]);

            // Simpan data kelompok ke dalam database menggunakan model
            return $this->subbukubesarRepository->insert($request);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            return $this->subbukubesarRepository->edit($id);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, Request $request)
    {
        try {

            $validatedData = $request->validate([
                'ket' => 'required|string',
            ]);
            return $this->subbukubesarRepository->update($id, $request);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubBukuBesar $subBukuBesar)
    {
        //
    }
}
