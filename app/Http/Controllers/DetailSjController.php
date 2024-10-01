<?php

namespace App\Http\Controllers;

use App\Http\Requests\Storedetail_sjRequest;
use App\Http\Requests\Updatedetail_sjRequest;
use App\Models\detail_sj;
use App\Repository\DetailSj\DetailSjRepository;
use Exception;
use Illuminate\Http\Request;

class DetailSjController extends Controller
{
    protected $detailsjRepository;

    public function __construct(DetailSjRepository $detailsjRepository)
    {
        $this->detailsjRepository = $detailsjRepository;
    }

    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Storedetail_sjRequest $request)
    {
        //
    }

    public function show(detail_sj $detail_sj)
    {
        //
    }

    public function edit(Request $request, $id_sj)
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
            return $this->detailsjRepository->edit($request, $id_sj);           
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan.');
        }
    }

    public function update(Updatedetail_sjRequest $request, detail_sj $detail_sj)
    {
        //
    }

    public function destroy(detail_sj $detail_sj)
    {
        //
    }
}
