<?php

namespace App\Http\Controllers;

use App\Http\Requests\Storedetail_poRequest;
use App\Http\Requests\Updatedetail_poRequest;
use App\Models\detail_po;
use App\Repository\DetailPo\DetailPoRepository;
use Exception;
use Illuminate\Http\Request;

class DetailPoController extends Controller
{
    protected $detailpoRepository;

    public function __construct(DetailPoRepository $detailpoRepository)
    {
        $this->detailpoRepository = $detailpoRepository;
    }

    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Storedetail_poRequest $request)
    {
        //
    }

    public function show(detail_po $detail_po)
    {
        //
    }

    public function edit(Request $request, $id_po)
    {
        $request->validate([
            'id.*' => 'required',
            'barang_id.*' => 'required',
            'nama_barang.*' => 'required',
            'stok.*' => 'required',
            'harga.*' => 'required',
            'diskon.*' => 'required',
            'total_harga.*' => 'required',
        ]);

        try {
            return $this->detailpoRepository->edit($request, $id_po);            
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan.');
        }
    }

    public function update(Updatedetail_poRequest $request, detail_po $detail_po)
    {
        //
    }

    public function destroy(detail_po $detail_po)
    {
        //
    }
}
