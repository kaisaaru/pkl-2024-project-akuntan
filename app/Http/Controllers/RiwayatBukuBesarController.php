<?php

namespace App\Http\Controllers;

use App\Models\BukuBesar;
use App\Models\RiwayatBukuBesar;
use App\Repository\RiwayatBukuBesar\RiwayatBukuBesarRepository;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Requests\StoreRiwayatBukuBesarRequest;
use App\Http\Requests\UpdateRiwayatBukuBesarRequest;
use App\Models\SubBukuBesar;

class RiwayatBukuBesarController extends Controller
{
    protected $riwayatbukubesarRepository;

    public function __construct(RiwayatBukuBesarRepository $riwayatbukubesarRepository)
    {
        $this->riwayatbukubesarRepository = $riwayatbukubesarRepository;
    }

    public function index($no_subbukubesar, Request $request)
    {
        try {
            return $this->riwayatbukubesarRepository->index($no_subbukubesar, $request);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function create()
    {
        //
    }

    public function store(StoreRiwayatBukuBesarRequest $request)
    {
        try {
            //
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error storing data.');
        }
    }

    public function show(RiwayatBukuBesar $riwayatBukuBesar)
    {
        //
    }

    public function edit(RiwayatBukuBesar $riwayatBukuBesar)
    {
        //
    }

    public function update(UpdateRiwayatBukuBesarRequest $request, RiwayatBukuBesar $riwayatBukuBesar)
    {
        //
    }

    public function destroy(RiwayatBukuBesar $riwayatBukuBesar)
    {
        //
    }
}
