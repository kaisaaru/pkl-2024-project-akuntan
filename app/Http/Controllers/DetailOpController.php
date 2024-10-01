<?php

namespace App\Http\Controllers;

use App\Http\Requests\Storedetail_opRequest;
use App\Http\Requests\Updatedetail_opRequest;
use App\Models\detail_op;
use App\Repository\DetailOp\DetailOpRepository;
use Exception;
use Illuminate\Http\Request;

class DetailOpController extends Controller
{
    protected $detailopRepository;

    public function __construct(DetailOpRepository $detailopRepository)
    {
        $this->detailopRepository = $detailopRepository;
    }

    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Storedetail_opRequest $request)
    {
        //
    }

    public function show(detail_op $detail_op)
    {
        //
    }

    public function edit(Request $request, $id_so)
    {
        $request->validate([
            'id.*' => 'required',
            'nama_barang.*' => 'required',
            'stok.*' => 'required',
            'harga.*' => 'required',
            'diskon.*' => 'required',
            'total_harga.*' => 'required',
        ]);

        try {
            return $this->detailopRepository->edit($request, $id_so);         
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan.');
        }
    }

    public function update(Updatedetail_opRequest $request, detail_op $detail_op)
    {
        //
    }

    public function destroy(detail_op $detail_op)
    {
        //
    }
}
