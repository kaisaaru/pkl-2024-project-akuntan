<?php

namespace App\Http\Controllers;

use App\Models\faktur_jual;
use App\Http\Requests\Storefaktur_jualRequest;
use App\Http\Requests\Updatefaktur_jualRequest;
use App\Repository\FakturJual\FakturJualRepository;
use Illuminate\Http\Request;

class FakturJualController extends Controller
{
    protected $fakturjualRepository;

    public function __construct(FakturJualRepository $fakturjualRepository)
    {
        $this->fakturjualRepository = $fakturjualRepository;
    }

    public function index()
    {
        try {
            return $this->fakturjualRepository->index();            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function createfj(Request $request, $id_sj)
    {
        try {
            return $this->fakturjualRepository->createfj($request, $id_sj);          
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function print($id_fj, $id_sj)
    {
        try {
            return $this->fakturjualRepository->print($id_fj, $id_sj);       
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function store(Storefaktur_jualRequest $request)
    {
        try {
            //
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function show(faktur_jual $faktur_jual)
    {
        try {
            //
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function edit(faktur_jual $faktur_jual)
    {
        try {
            //
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function update(Updatefaktur_jualRequest $request, faktur_jual $faktur_jual)
    {
        try {
            //
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function destroy(faktur_jual $faktur_jual)
    {
        try {
            //
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function status($status, $id)
    {
        try {
            return $this->fakturjualRepository->status($status, $id);         
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
