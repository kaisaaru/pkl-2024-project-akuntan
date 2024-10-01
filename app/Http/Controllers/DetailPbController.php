<?php

namespace App\Http\Controllers;

use App\Models\detail_pb;
use App\Http\Requests\Storedetail_pbRequest;
use App\Http\Requests\Updatedetail_pbRequest;
use App\Repository\DetailPb\DetailPbRepository;
use Exception;
use Illuminate\Http\Request;

class DetailPbController extends Controller
{
    protected $detailpbRepository;

    public function __construct(DetailPbRepository $detailpbRepository)
    {
        $this->detailpbRepository = $detailpbRepository;
    }

    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Storedetail_pbRequest $request)
    {
        //
    }

    public function show(detail_pb $detail_pb)
    {
        //
    }

    public function edit(Request $request, $id_pb)
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
            return $this->detailpbRepository->edit($request, $id_pb);          
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan.');
        }
    }

    public function update(Updatedetail_pbRequest $request, detail_pb $detail_pb)
    {
        //
    }

    public function destroy(detail_pb $detail_pb)
    {
        //
    }
}
