<?php

namespace App\Http\Controllers;

use App\Http\Requests\Storefaktur_beliRequest;
use App\Http\Requests\Updatefaktur_beliRequest;
use App\Models\detail_faktur;
use App\Models\detail_pb;
use App\Models\faktur_beli;
use App\Models\faktur_line;
use App\Models\PenerimaanBarang;
use App\Models\PurchaseOrder;
use App\Models\Perusahaan;
use App\Models\SubBukuBesar;
use App\Models\BukuBesar;
use App\Models\RiwayatBukuBesar;
use App\Models\TipeAkun;
use App\Models\Neraca;
use App\Repository\FakturBeli\FakturBeliRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FakturBeliController extends Controller
{
    protected $fakturbeliRepository;

    public function __construct(FakturBeliRepository $fakturbeliRepository)
    {
        $this->fakturbeliRepository = $fakturbeliRepository;
    }

    public function index()
    {
        try {
            $fb = faktur_beli::paginate(20);
            $detail = detail_faktur::with('subbukubesar')->get();
            return view('barang.barangmasuk.faktur.dataFB', compact('fb', 'detail'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function createfb(Request $request, $id_pb)
    {        
        
        try {
            return $this->fakturbeliRepository->createfb($request, $id_pb);       
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function print($id_fb, $id_pb)
    {
        try {
            return $this->fakturbeliRepository->print($id_fb, $id_pb);         
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function store(Storefaktur_beliRequest $request)
    {
        try {
            //
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function show(faktur_beli $faktur_beli)
    {
        try {
            //
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function edit(faktur_beli $faktur_beli)
    {
        try {
            //
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function update(Updatefaktur_beliRequest $request, faktur_beli $faktur_beli)
    {
        try {
            //
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function destroy(faktur_beli $faktur_beli)
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
            return $this->fakturbeliRepository->status($status, $id);        
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
